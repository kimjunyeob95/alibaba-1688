<?php

namespace App\Packages;

use App\Abstracts\MallApiAbstract;
use App\Constants\EasySellConstant;
use App\Constants\MallConstant;
use App\Constants\MallErrorMessageConstant;
use App\Constants\ProductConstant;
use App\Models\EasysellProductLog;
use App\Models\OnchCategoryExcelDataCopy2;
use App\Models\ProductData;
use App\Vo\EasySell\EasySellProductVo;
use Carbon\Carbon;
use Exception;

class EasySell extends MallApiAbstract
{
    public function __construct(string $channel)
    {
        parent::__construct(app(JwtPackage::class), $channel);
    }

    /**
     * @func productRegist
     * @description '상품등록'
     * @param array $offerIds
     * @return array
    */
    public function productRegist(array $offerIds): array
    {
        $successIds = [];
        $failIds    = [];

        foreach ($offerIds as $offerId) {
            $easyObj      = null;
            $itemno       = 0;
            $account      = EasySellConstant::USER_ID;
            $modi_success = MallConstant::MODI_FAIL;
            $modi_message = "";
            try{
                $easyObj = EasySellProductLog::where([
                    "offer_id"       => $offerId,
                    "regist_success" => MallConstant::REGIST_SUCCESS,
                ])->first();
                if( $easyObj != null ){
                    throw new Exception(MallErrorMessageConstant::getFitErrorMessage("HAVE_REGIST"));
                }

                $prdObj = ProductData::with([
                    "images",
                    "extends",
                    "options",
                    "notices",
                    "oc_mapping"
                ])->where("offer_id", $offerId)->first();

                if( $prdObj == null ){
                    throw new Exception(MallErrorMessageConstant::getNotHaveErrorMessage("PRODUCT"));
                }
                if( $prdObj->trans_status != ProductConstant::IMG_TRANS_Y ){
                    throw new Exception(MallErrorMessageConstant::getFitErrorMessage("NOT_TRANS_IMG"));
                }

                $paramsResult = $this->_getPrdParams($prdObj, EasySellConstant::ITEM_REGIST);
                if( $paramsResult["isSuccess"] == true ){
                    $apiResult = $this->_apiCall("Goods", $paramsResult["data"]);

                    if( $apiResult["isSuccess"] != true ){
                        throw new Exception(MallErrorMessageConstant::getFitErrorMessage("EASYSELL_GOODS_API"));
                    }

                    $rsData = $apiResult["data"]["result"];

                    if($rsData->Result == EasySellConstant::API_SUCCESS){
                        $itemno       = $rsData->ItemGoodCode;
                        $successIds[] = $offerId;
                    } else {
                        throw new Exception($rsData->Msg);
                    }

                    $logParams = [
                        "itemno"         => $itemno,
                        "offer_id"       => $offerId,
                        "account"        => $account,
                        "regist_success" => MallConstant::REGIST_SUCCESS,
                        "regist_message" => $rsData->Msg,
                        "modi_success"   => $modi_success,
                        "modi_message"   => $modi_message,
                        "registed_at"    => Carbon::now()
                    ];
                } else {
                    throw new Exception($paramsResult["msg"]);
                }
            }catch(Exception $e){
                $logParams = [
                    "itemno"         => $itemno,
                    "offer_id"       => $offerId,
                    "account"        => $account,
                    "regist_success" => MallConstant::REGIST_FAIL,
                    "regist_message" => $e->getMessage(),
                    "modi_success"   => $modi_success,
                    "modi_message"   => $modi_message
                ];

                $failIds[] = [
                    "offer_id" => $offerId,
                    "msg"      => $e->getMessage()
                ];
            }

            if ( $easyObj == null ){
                EasysellProductLog::updateOrCreate(["offer_id" => $offerId], $logParams);
            }
        }

        $result = ["success" => $successIds, "fail" => $failIds];

        return helpers_success_message($result);
    }

    public function orderInfo(int $orderId): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $returnMsg = helpers_success_message(["orderId" => $orderId]);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function orderCreate(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $returnMsg = helpers_success_message($params);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * api param 생성 - 상품 등록/수정 공통사용
     *
     * @param ProductData $prdObj
     * @param string $itemMode
     * @return array
     */
    private function _getPrdParams(ProductData $prdObj, string $itemMode = EasySellConstant::ITEM_REGIST) :array
    {
        $return = helpers_fail_message();

        try{
            $offerId = $prdObj->offer_id;

            //카테고리 매핑
            $categoryId = $prdObj->oc_mapping->mapping_code;
            $cateObj = OnchCategoryExcelDataCopy2::select("sellerhub_cate")->where("codenum",$categoryId)->first();
            if(!isset($cateObj)){
                throw new Exception("카테고리 정보가 없습니다");
            }

            $ItemName = $prdObj->prd_name_trans;
            // if(!productNameValidation($ItemName, "", 100)){
            //     throw new Exception("상품명 길이가 100byte를 초과했습니다.");
            // }

            $ItemDescDetail = "<p style='font-size:18px;border: 1px solid #ec9821;background-color: #f0ad4e; color: #fff;padding: 15px 30px;'>{$prdObj->return_comment}</p>{$prdObj->prd_desc_trans}";

            $notice = "<table><tbody>";
            foreach($prdObj->notices as $gosiKey => $gosi){
                if(!$gosiKey){
                    $noticeType = $gosi->notice_type;
                }

                if( $gosiKey % 4 == 0){
                    $notice .="<tr>";
                }

                $notice .= "<th>{$gosi->attribute_name_trans}</th><td>{$gosi->attribute_value_trans}</td>";

                if(($gosiKey + 1) % 4 == 0 || ($gosiKey + 1) == count($prdObj->notices)){
                    $notice .="</tr>";
                }
            }
            $notice .= "</tbody></table>";

            $unitInfo   = "옵션|";
            $saleStatus = EasySellConstant::STATUS_STOP_SALE;
            foreach($prdObj->options as $idx => $option){
                if(!$idx){
                    $buyPrice  = $option->option_price; //셀러허브 공급가
                    $salePrice = $setPrice = ceil(($option->onch_price * env("EASYSELL_PRICE_RATE", "1.35")) / 100) * 100;
                }else{
                    $unitInfo .= ",";
                }
                $setPrice = ceil(($option->onch_price * env("EASYSELL_PRICE_RATE", "1.35")) / 100) * 100;

                //옵션명
                $replaceArr     = array("|",",","/");
                $replacementArr = array("-","\,","-");
                $optionNm       = str_replace($replaceArr, $replacementArr ,$option->option_name_trans);

                if($option->status == ProductConstant::OPTION_SEC_ON_SALE_NUMBER){
                    $saleStatus = EasySellConstant::STATUS_ON_SALE;
                    $stock      = $option->amount_on_sale;
                }
                $stock = 0;

                $unitInfo .= "{$optionNm}^^{$stock}^^{$setPrice}^^{$setPrice}^^{$option->option_price}::{$option->id}";
            }

            $itemImage = implode("|", array_reverse(array_filter($prdObj->images->whereIn("img_type",["main","sub"])->pluck("img_url_trans")->toArray())));

            $voParams = [
                "ItemNo"                => $offerId,
                "ItemCategory"          => $cateObj->sellerhub_cate,
                "ItemName"              => $ItemName,
                "ItemDesc"              => $ItemName,
                "ItemDescDetail"        => $ItemDescDetail,
                "ItemGoodsRequiredDesc" => $notice,
                "ItemImage"             => $itemImage,
                "TaxYn"                 => ($prdObj->tax_type) == ProductConstant::TAX_TAXATION ? EasySellConstant::TAX_TAXATION : EasySellConstant::TAX_EXEMPTION,
                "BuyPrice"              => $buyPrice,
                "SalePrice"             => $salePrice,
                "ConsumerPrice"         => $salePrice,
                "Delfee"                => $prdObj->extends->send_default_price,
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
    private function _iconvArr (&$item) {
        $item = iconv("utf-8","euc-kr//TRANSLIT",$item);
    }

    private function _apiCall(string $name, array $params = []): array
	{
        $returnMsg = $this->returnMsg;

        try {
            $url = sprintf('%s.%s.php',  env("EASYSELL_DOMAIN", "https://pravs.co.kr/shop/_OpenAPI/link"), $name);

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

            $response = str_replace('euc-kr', 'utf-8', $response);
            $response = mb_convert_encoding($response, 'utf-8', 'euc-kr');


            if (false) {
                $response = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $response);
                $response = preg_replace('/[a-zA-Z0-9]+:([a-zA-Z]+[=>]*)/', '$1', $response);
                $response = preg_replace('/&(?!lt;|gt;|quot;|apos;|amp;|#)/', '&amp;', $response);
            }

            $response = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
            if(empty($response)){
                throw new Exception(MallErrorMessageConstant::getFitErrorMessage("XML_PARSE"));
            }

            $returnMsg = helpers_success_message(["result" => $response]);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }
}
