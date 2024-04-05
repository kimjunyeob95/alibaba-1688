<?php

namespace App\Packages;

use App\Abstracts\MallApiAbstract;
use App\Constants\EasySellConstant;
use App\Constants\ProductConstant;
use App\Models\EasysellProductLog;
use App\Models\OnchCategoryExcelDataCopy2;
use App\Models\ProductData;
use App\Vo\EasySell\EasySellProductVo;
use Carbon\Carbon;
use Exception;
use SimpleXMLElement;

class EasySell extends MallApiAbstract
{
    public function __construct()
    {
    }

    public function productRegist(): array
    {
        $selObj = ProductData::where("trans_status","Y")
            ->whereNotIn("offer_id",function($query){
                $query->select("offer_id")->from("easysell_product_logs");
            })
            ->first();
        if(!isset($selObj)){
            return helpers_fail_message(false, "전송 가능한 상품이 없습니다");
        }

        try{
            $offerId = $selObj->offer_id;
            $paramsResult = $this->_getPrdParams($offerId, EasySellConstant::ITEM_REGIST);

            if( $paramsResult["isSuccess"] ){
                $rsData = $this->_apiCall("Goods",$paramsResult["data"]);

                if($rsData->Result == EasySellConstant::API_SUCCESS){
                    $result = "Y";
                    $itemno = $rsData->ItemGoodCode;
                    $registedAt = Carbon::now();

                    $return = helpers_success_message();
                }else{
                    $result = "N";
                    $itemno = $rsData->ItemGoodNo;
                    $registedAt = NULL;

                    $return = helpers_fail_message(false, $rsData->Msg);
                }

                $logParams = [
                    "itemno"         => $itemno,
                    "offer_id"       => $offerId,
                    "account"        => EasySellConstant::USER_ID,
                    "regist_success" => $result,
                    "regist_message" => $rsData->Msg,
                    "payload_json"   => json_encode(mb_convert_encoding($paramsResult["data"],'utf-8','euc-kr'), JSON_UNESCAPED_UNICODE),
                    "response_json"  => json_encode($rsData,JSON_UNESCAPED_UNICODE),
                    "registed_at"    => $registedAt
                ];

                EasysellProductLog::updateOrCreate(["offer_id" => $offerId], $logParams);
            } else {
                throw new Exception($paramsResult["msg"]);
            }
        }catch(Exception $e){
            $return = helpers_fail_message(false, $e->getMessage());
        }

        return $return;
    }

    public function getOrders(): array
    {
        return ["result" => "getOrders / EasySell"];
    }

    private function _getPrdParams(int $offerId, string $itemMode = EasySellConstant::ITEM_REGIST) :array
    {
        $return = helpers_fail_message();

        try{
            $productObj = ProductData::where("offer_id",$offerId);
            if(!$productObj->exists()){
                throw new Exception("존재하지 않는 상품입니다");
            }
            $productObj->where("trans_status",ProductConstant::IMG_TRANS_Y);
            if(!$productObj->exists()){
                throw new Exception("번역 미완료 상품입니다");
            }

            $productObj = $productObj->with([
                "images",
                "extends",
                "options",
                "notices",
                "oc_mapping"
            ])
            ->first();

            //카테고리 매핑
            $categoryId = $productObj->oc_mapping->mapping_code;
            $cateObj = OnchCategoryExcelDataCopy2::select("sellerhub_cate")->where("codenum",$categoryId)->first();
            if(!isset($cateObj)){
                throw new Exception("카테고리 정보가 없습니다");
            }

            $ItemName = $productObj->prd_name_trans;
            // if(!productNameValidation($ItemName, "", 100)){
            //     throw new Exception("상품명 길이가 100byte를 초과했습니다.");
            // }

            $ItemDescDetail = "<p style='font-size:18px;border: 1px solid #ec9821;background-color: #f0ad4e; color: #fff;padding: 15px 30px;'>{$productObj->return_comment}</p>{$productObj->prd_desc_trans}";

            $notice = "<table><tbody>";
            foreach($productObj->notices as $gosiKey => $gosi){
                if(!$gosiKey){
                    $noticeType = $gosi->notice_type;
                }

                if( $gosiKey % 4 == 0){
                    $notice .="<tr>";
                }

                $notice .= "<th>{$gosi->attribute_name_trans}</th><td>{$gosi->attribute_value_trans}</td>";

                if(($gosiKey + 1) % 4 == 0 || ($gosiKey + 1) == count($productObj->notices)){
                    $notice .="</tr>";
                }
            }
            $notice .= "</tbody></table>";

            $unitInfo = "";
            $saleStatus = EasySellConstant::STATUS_STOP_SALE;
            foreach($productObj->options as $idx => $option){
                if(!$idx){
                    $buyPrice = $option->option_price; //셀러허브 공급가
                    $salePrice = $setPrice = ceil(($option->onch_price * env("EASYSELL_PRICE_RATE", "1.35")) / 100) * 100;
                }
                $setPrice = ceil(($option->onch_price * env("EASYSELL_PRICE_RATE", "1.35")) / 100) * 100;

                //옵션명
                $replaceArr = array("|",",","/");
                $replacementArr = array("-","\,","-");
                $optionNm = str_replace($replaceArr, $replacementArr ,$option->option_name_trans);

                if($option->status == ProductConstant::OPTION_SEC_ON_SALE_NUMBER){
                    $saleStatus = EasySellConstant::STATUS_ON_SALE;
                    $stock = $option->amount_on_sale;
                }
                $stock = 0;

                $unitInfo .= "{$optionNm}^^{$stock}^^{$setPrice}^^{$setPrice}^^{$option->option_price}::{$option->id}";
            }

            $itemImage = implode("|", array_filter($productObj->images->whereIn("img_type",["main","sub"])->pluck("img_url_trans")->toArray()));

            $voParams = [
                "ItemNo"                => $offerId,
                "ItemCategory"          => $cateObj->sellerhub_cate,
                "ItemName"              => $ItemName,
                "ItemDesc"              => $ItemName,
                "ItemDescDetail"        => $ItemDescDetail,
                "ItemGoodsRequiredDesc" => $notice,
                "ItemImage"             => $itemImage,
                "TaxYn"                 => ($productObj->tax_type) == 1 ? "001" : "002",   //과세 001, 면세 002
                "BuyPrice"              => $buyPrice,
                "SalePrice"             => $salePrice,
                "ConsumerPrice"         => $salePrice,
                "Delfee"                => $productObj->extends->send_default_price,
                "UnitInfo"              => $unitInfo,
                "SaleStatus"            => $saleStatus,
                "ItemMode"              => $itemMode,
                "noticeType"            => $noticeType
            ];

            $vo = new EasySellProductVo();
            $vo->bind($voParams);

            $apiParams = [
                "LinkerID"                   => $vo->LinkerID,
                "UserID"                     => $vo->UserID,
                "UserPW"                     => $vo->UserPW,
                "ItemNo"                     => $vo->ItemNo,
                "ItemPartnerCode"            => $vo->ItemPartnerCode,
                "ItemCategory"               => $vo->ItemCategory,
                "ItemBrand"                  => $vo->ItemBrand,
                "ItemName"                   => $vo->ItemName,
                "ItemGoodCode"               => $vo->ItemGoodCode,
                "Launchdate"                 => $vo->Launchdate,
                "ItemMaker"                  => $vo->ItemMaker,
                "ItemMadeIn"                 => $vo->ItemMadeIn,
                "ItemDesc"                   => $vo->ItemDesc,
                "ItemDescDetail"             => $vo->ItemDescDetail,
                "ItemGoodsRequiredDesc"      => $vo->ItemGoodsRequiredDesc,
                "ItemImage"                  => $vo->ItemImage,
                "ItemImageUpt"               => $vo->ItemImageUpt,
                "TaxYn"                      => $vo->TaxYn,
                "BuyPrice"                   => $vo->BuyPrice,
                "MarginRate"                 => $vo->MarginRate,
                "SalePrice"                  => $vo->SalePrice,
                "ConsumerPrice"              => $vo->ConsumerPrice,
                "DeliveryCharge"             => $vo->DeliveryCharge,
                "Delfee"                     => $vo->Delfee,
                "Unittype"                   => $vo->Unittype,
                "UnitInfo"                   => $vo->UnitInfo,
                "OptPrice"                   => $vo->OptPrice,
                "SaleStatus"                 => $vo->SaleStatus,
                "StockYn"                    => $vo->StockYn,
                "ItemMode"                   => $vo->ItemMode,
                "ItemNoRefund"               => $vo->ItemNoRefund,
                "ItemReturnQualityAssurance" => $vo->ItemReturnQualityAssurance,
                "ItemCompensation"           => $vo->ItemCompensation,
                "ItemTroubleShooting"        => $vo->ItemTroubleShooting,
                "ItemCertification"          => $vo->ItemCertification,
                "ItemCertificationInfo"      => $vo->ItemCertificationInfo,
                "ItemApproveAuto"            => $vo->ItemApproveAuto,
            ] + $vo->ItemGoodsRequired;

            ###인코딩
            array_walk_recursive($apiParams, array($this, "_iconvArr"));

            $return = helpers_success_message($apiParams);
        } catch(Exception $e){
            $return = helpers_fail_message(false, $e->getMessage());
        }

        return $return;
    }

    /**
	 * 인코딩 변경
	 *
     * @param $item 변경할 array
	 *
     */
    private function _iconvArr (&$item, $key) {
        $item = iconv("utf-8","euc-kr//TRANSLIT",$item);
    }

    /**
     * @param string $name
     * @return string
     */
    private function _getApiHost(string $name): string
    {
        return sprintf('%s.%s.php',  env("EASYSELL_DOMAIN", "https://pravs.co.kr/shop/_OpenAPI/link"), $name);
    }

    /**
     * @param string $xml
     * @param bool   $removeNameSpace
     * @return SimpleXMLElement
     */
    function parseXml(string $xml, bool $removeNameSpace = true): ?SimpleXMLElement
    {
        if ($removeNameSpace) {
            $xml = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $xml);
            $xml = preg_replace('/[a-zA-Z0-9]+:([a-zA-Z]+[=>]*)/', '$1', $xml);
			$xml = preg_replace('/&(?!lt;|gt;|quot;|apos;|amp;|#)/', '&amp;', $xml); //xml escape 추가
        }

		$response = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
		if(empty($response)){
			$response = null;
		}

        return $response;
    }


    private function _apiCall(string $name, array $params = []) :?SimpleXMLElement
	{
        $url = $this->_getApiHost($name);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_HTTPHEADER => array(
            )
        ));

       	$response = curl_exec($curl);
    	curl_close($curl);

		$response = str_replace('euc-kr','utf-8',$response);
		$response = mb_convert_encoding($response,'utf-8','euc-kr');

        $rsData = $this->parseXml($response);

        return $rsData;
    }
}
