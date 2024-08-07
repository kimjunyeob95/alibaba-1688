<?php

namespace App\Packages;

use App\Abstracts\MallApiAbstract;
use App\Abstracts\OrderAbstract;
use App\Abstracts\ProductAbstract;
use App\Abstracts\TransApiAbstract;
use App\Constants\CategoryConstant;
use App\Constants\EasySellConstant;
use App\Constants\MallConstant;
use App\Constants\MallErrorMessageConstant;
use App\Constants\OptionConstant;
use App\Constants\ProductConstant;
use App\Models\CategoryMapping;
use App\Models\ChannelCategoryRegistData;
use App\Models\EasysellProductDetailLog;
use App\Models\EasysellProductLog;
use App\Models\ProductData;
use App\Models\ProductModiData;
use App\Models\ProductWeightData;
use App\Models\SellerhubCategory;
use App\Vo\EasySell\EasySellProductVo;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class EasySell extends MallApiAbstract
{
    public function __construct(
        JwtPackage $jwtPackage,
        string $channel,
        OrderAbstract $orderW1,
        TransApiAbstract $transApiAbstract,
        ProductAbstract $productW1
    )
    {
        parent::__construct($jwtPackage, $channel, $orderW1, $transApiAbstract, $productW1);
    }

    /****************************************** 상품 start **********************************************/

    /**
     * @func productRegist
     * @description '상품등록'
     * @param array $offerIds
     * @param array $params
     * @return array
    */
    public function productRegist(array $offerIds, array $params = []): array
    {
        $successIds = [];
        $failIds    = [];
        $updateIds  = [];
        $type = [ EasySellConstant::TYPE_W ];
        if( isset($params["type"]) ){
            $type = $params["type"];
        }
        foreach($type as $w_type){
            $updateIds  = [];
            foreach ($offerIds as $offerId) {
                $easyObj      = null;
                $itemno       = 0;
                $account      = EasySellConstant::USER_ID_W;
                $modi_success = MallConstant::MODI_FAIL;
                $modi_message = "";
                try{
                    switch($w_type){
                        case EasySellConstant::TYPE_W :
                            $account = EasySellConstant::USER_ID_W;
                            break;
                        case EasySellConstant::TYPE_DROPHUB:
                            $account = EasySellConstant::USER_ID_DROPHUB;
                            break;
                        default :
                            throw new Exception(MallErrorMessageConstant::getNotHaveErrorMessage("TYPE"));
                            break;
                    }

                    $easyObj = EasysellProductLog::where([
                        "offer_id"       => $offerId,
                        "regist_success" => MallConstant::REGIST_SUCCESS,
                        "account"        => $account,
                    ])->first();
                    if( $easyObj != null ){
                        // throw new Exception(MallErrorMessageConstant::getFitErrorMessage("HAVE_REGIST"));
                        $updateIds[] = $offerId;
                        continue;
                    }

                    $prdObj = ProductData::with([
                        "main_img",
                        "no_except_sub_imgs",
                        "en_main_img",
                        "no_except_en_sub_imgs",
                        "extends",
                        "no_except_options",
                        "no_except_notices",
                        "es_mapping",
                        "es_fgn_mapping"
                    ])->where("offer_id", $offerId)->first();

                    if( $prdObj == null ){
                        throw new Exception(MallErrorMessageConstant::getNotHaveErrorMessage("PRODUCT"));
                    }

                    $channelCnt = ChannelCategoryRegistData::where([
                        "channel"     => $this->channel,
                        "send_type"   => $w_type,
                        "category_id" => $prdObj->category_id,
                        "is_regist"   => MallConstant::REGIST_Y,
                    ])->count();

                    if( $channelCnt == 0 ){
                        throw new Exception(MallErrorMessageConstant::getFitErrorMessage("CATEGORY_REGIST"));
                    }

                    if( ($w_type == EasySellConstant::TYPE_W) && $prdObj->trans_status != ProductConstant::IMG_TRANS_Y ){
                        throw new Exception(MallErrorMessageConstant::getFitErrorMessage("NOT_TRANS_IMG"));
                    }
                    if( $prdObj->mapping_status != ProductConstant::MAPPING_STATUS_Y ){
                        throw new Exception(MallErrorMessageConstant::getFitErrorMessage("NOT_MAPPING_CATE"));
                    }
                    if( count($prdObj->no_except_options) == 0 ){
                        throw new Exception(MallErrorMessageConstant::getFitErrorMessage("OPTION"));
                    }

                    $paramsResult = $this->_getPrdParams($prdObj, $w_type, EasySellConstant::ITEM_REGIST);
                    if( $paramsResult["isSuccess"] == true ){
                        $apiResult = $this->_apiCall("Goods", $paramsResult["data"]);

                        if( $apiResult["isSuccess"] != true ){
                            throw new Exception(MallErrorMessageConstant::getFitErrorMessage("EASYSELL_GOODS_API"));
                        }

                        $rsData = $apiResult["data"]["result"];
                        $itemno = $rsData->ItemGoodCode;

                        if($rsData->Result == EasySellConstant::API_SUCCESS){
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

                        $detailParams = [
                            "log_id"     => 0,
                            "send_type"  => MallConstant::SEND_TYPE_REGIST,
                            "is_success" => MallConstant::REGIST_SUCCESS,
                            "message"    => $rsData->Msg,
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

                    $detailParams = [
                        "log_id"     => 0,
                        "send_type"  => MallConstant::SEND_TYPE_REGIST,
                        "is_success" => MallConstant::REGIST_FAIL,
                        "message"    => $e->getMessage(),
                    ];

                    $failIds[] = [
                        "offer_id" => $offerId,
                        "msg"      => $e->getMessage()
                    ];
                }

                if ( $easyObj == null ){
                    $selLog = EasysellProductLog::updateOrCreate([
                        "offer_id" => $offerId,
                        "w_type"   => $w_type
                    ], $logParams);

                    $detailParams['log_id'] = $selLog->id;
                    EasysellProductDetailLog::create($detailParams);
                }
            }

            if(count($updateIds)){
                $updateResult = $this->productModi($updateIds, $w_type);

                $failIds    += $updateResult['data']['fail'];
                $successIds += $updateResult['data']['success'];
            }
        }

        $result = ["success" => $successIds, "fail" => $failIds];

        return helpers_success_message($result);
    }

    /**
     * @func productModi
     * @description '상품수정'
     * @param array $offerIds
     * @param string $type
     * @return array
    */
    public function productModi(array $offerIds, string $type): array
    {
        $successIds = [];
        $failIds    = [];

        foreach ($offerIds as $offerId) {
            try{
                switch($type){
                    case EasySellConstant::TYPE_W :
                        $account      = EasySellConstant::USER_ID_W;
                        break;
                    case EasySellConstant::TYPE_DROPHUB:
                        $account      = EasySellConstant::USER_ID_DROPHUB;
                        break;
                    default :
                        throw new Exception(MallErrorMessageConstant::getNotHaveErrorMessage("TYPE"));
                        break;
                }

                $easyObj = EasysellProductLog::where([
                    "offer_id"       => $offerId,
                    "regist_success" => MallConstant::REGIST_SUCCESS,
                    "account"        => $account,
                ])->first();
                if( $easyObj == null ){
                    throw new Exception(MallErrorMessageConstant::getFitErrorMessage("MODI_UNREGIST"));
                }

                $prdObj = ProductData::with([
                    "main_img",
                    "no_except_sub_imgs",
                    "en_main_img",
                    "no_except_en_sub_imgs",
                    "extends",
                    "no_except_options",
                    "no_except_notices",
                    "es_mapping",
                    "es_fgn_mapping"
                ])->where("offer_id", $offerId)->first();

                if( $prdObj == null ){
                    throw new Exception(MallErrorMessageConstant::getNotHaveErrorMessage("PRODUCT"));
                }

                $channelCnt = ChannelCategoryRegistData::where([
                    "channel"     => $this->channel,
                    "send_type"   => $type,
                    "category_id" => $prdObj->category_id,
                    "is_regist"   => MallConstant::REGIST_Y,
                ])->count();

                if( $channelCnt == 0 ){
                    throw new Exception(MallErrorMessageConstant::getFitErrorMessage("CATEGORY_REGIST"));
                }

                if( ($type == EasySellConstant::TYPE_W) && $prdObj->trans_status != ProductConstant::IMG_TRANS_Y ){
                    throw new Exception(MallErrorMessageConstant::getFitErrorMessage("NOT_TRANS_IMG"));
                }
                if( $prdObj->mapping_status != ProductConstant::MAPPING_STATUS_Y ){
                    throw new Exception(MallErrorMessageConstant::getFitErrorMessage("NOT_MAPPING_CATE"));
                }
                if( count($prdObj->no_except_options) == 0 ){
                    throw new Exception(MallErrorMessageConstant::getFitErrorMessage("OPTION"));
                }

                $paramsResult = $this->_getPrdParams($prdObj, $type, EasySellConstant::ITEM_MODI, $easyObj->itemno);
                if( $paramsResult["isSuccess"] == true ){
                    $apiResult = $this->_apiCall("Goods", $paramsResult["data"]);

                    if( $apiResult["isSuccess"] != true ){
                        throw new Exception(MallErrorMessageConstant::getFitErrorMessage("EASYSELL_GOODS_API"));
                    }

                    $rsData = $apiResult["data"]["result"];

                    if($rsData->Result == EasySellConstant::API_SUCCESS){
                        $successIds[] = $offerId;
                    } else {
                        throw new Exception($rsData->Msg);
                    }

                    $logParams = [
                        "offer_id"     => $offerId,
                        "account"      => $account,
                        "modi_success" => MallConstant::MODI_SUCCESS,
                        "modi_message" => $rsData->Msg,
                        "modied_at"    => Carbon::now(),
                    ];

                    $detailParams = [
                        "log_id"     => 0,
                        "send_type"  => MallConstant::SEND_TYPE_MODI,
                        "is_success" => MallConstant::MODI_SUCCESS,
                        "message"    => $rsData->Msg,
                    ];
                } else {
                    throw new Exception($paramsResult["msg"]);
                }
            }catch(Exception $e){
                $logParams = [
                    "offer_id"     => $offerId,
                    "account"      => $account,
                    "modi_success" => MallConstant::MODI_FAIL,
                    "modi_message" => $e->getMessage(),
                    "modied_at"    => Carbon::now(),
                ];

                $detailParams = [
                    "log_id"     => 0,
                    "send_type"  => MallConstant::SEND_TYPE_MODI,
                    "is_success" => MallConstant::MODI_FAIL,
                    "message"    => $e->getMessage(),
                ];

                $failIds[] = [
                    "offer_id" => $offerId,
                    "msg"      => $e->getMessage()
                ];
            }

            $logObj = EasysellProductLog::where([
                "offer_id" => $offerId,
                "w_type"   => $type
            ])->first();
            $logObj->update($logParams);

            $detailParams['log_id'] = $logObj->id;
            EasysellProductDetailLog::create($detailParams);
        }

        $result = ["success" => $successIds, "fail" => $failIds];

        return helpers_success_message($result);
    }

    /**
     * @func sendModiProduct
     * @description '수정 된 상품 전송'
     * @return void
     */
    public function sendModiProduct(): void
    {
        $now      = Carbon::now();
        $modiObjs = ProductModiData::where("is_send", ProductConstant::IS_SEND_N)
        ->where("channel", MallConstant::MALL_EASYSELL)
        ->groupBy("offer_id", "w_type")
        ->get();

        // $msg = "수정 된 상품 전송 배치 시작";
        // debug_log($msg, "easysell/sendModiProduct", "sendModiProduct");

        foreach ($modiObjs as $modiObj) {
            $param = [
                "type" => [$modiObj->w_type]
            ];
            $result = $this->productRegist([$modiObj->offer_id], $param);

            $query = ProductModiData::where("is_send", ProductConstant::IS_SEND_N)
            ->where("created_at", "<=", $now)
            ->where("offer_id", $modiObj->offer_id)
            ->where("channel", MallConstant::MALL_EASYSELL)
            ->where("w_type", $modiObj->w_type);

            if( in_array($modiObj->offer_id, $result["data"]["success"]) ){
                // 1. 전송 성공 시
                $query->update([
                    "is_send"       => ProductConstant::IS_SEND_Y,
                    "send_dated_at" => Carbon::now()
                ]);
            } else {
                // 2. 전송 에러 시
                $query->update([
                    "is_send" => ProductConstant::IS_SEND_E,
                    "msg"     => $result["data"]["fail"][0]['msg']
                ]);
            }
        }

        // $msg = "수정 된 상품 전송 배치 종료";
        // debug_log($msg, "easysell/sendModiProduct", "sendModiProduct");
    }

    /**
     * 이지셀 판매중단처리 api
     *
     * @return void
     */
    public function setGoodsStatus(string $type,int $ItemGoodCode)
    {
        $vo = new EasySellProductVo($type);
        $vo->bind(["ItemGoodCode" => $ItemGoodCode]);

        $params = [
			"LinkerID"     => $vo->LinkerID,
			"UserID"       => $vo->UserID,
			"UserPW"       => $vo->UserPW,
			"ItemGoodCode" => $vo->ItemGoodCode,
			"SaleStatus"   => EasySellConstant::STATUS_STOP_SALE,
			"Soldout"      => "N"
        ];

        $apiResult = $this->_apiCall("GoodsStatus",$params);
        if( $apiResult["isSuccess"] != true ){
            throw new Exception(MallErrorMessageConstant::getFitErrorMessage("EASYSELL_GOODS_API"));
        }
    }

    /**
     * api param 생성 - 상품 등록/수정 공통사용
     *
     * @param Model $prdObj
     * @param string $itemMode
     * @param string $type
     * @param integer|null $ItemGoodCode - 수정시 필수
     * @return array
     */
    private function _getPrdParams(Model $prdObj, string $type, string $itemMode = EasySellConstant::ITEM_REGIST, ?int $ItemGoodCode = NULL): array
    {
        $return = helpers_fail_message();

        try{
            $offerId = $prdObj->offer_id;

            //카테고리 매핑
            if(!isset($prdObj->es_fgn_mapping) || empty($prdObj->es_fgn_mapping->mapping_code)){
                throw new Exception("카테고리 정보가 없습니다");
            }
            $categoryId = $prdObj->es_fgn_mapping->mapping_code;
            if(isset($prdObj->es_mapping) || !empty($prdObj->es_mapping->mapping_code)){
                $categoryId .= "|".$prdObj->es_mapping->mapping_code;
            }
            $ItemBrand  = EasySellConstant::CATEGORY_MAPPING[substr($prdObj->es_fgn_mapping->mapping_code,0,6)];
            $noticeType = $this->_getNoticeType($prdObj->es_fgn_mapping->mapping_code);

            //연령제한 상품여부
            if($prdObj->minor_not_sale == ProductConstant::MINOR_NOT_SALE_YES){
                throw new Exception("연령제한 상품입니다");
            }

            $images = [];
            if($type == EasySellConstant::TYPE_W){
                $ItemName    = $prdObj->prd_name_kr;
                $prdDesc     = $prdObj->prd_desc_kr;
                $optionTitle = "옵션";
                $noticeInfo  = $prdObj->no_except_notices->pluck("attribute_value_kr","attribute_name_kr")->toArray();

                $images[] = $prdObj->main_img->img_url_trans;
                foreach ($prdObj->no_except_sub_imgs as $imgObj) {
                    if( $imgObj->img_url_trans ){
                        $images[] = $imgObj->img_url_trans;
                    }
                }

                //배송비 설정
                $weights   = CategoryConstant::WEIGHTS;
                $weightObj = ProductWeightData::where("offer_id", $offerId)->first();
                $delivery_price = ProductConstant::WEIGHT_STATUS_NONE_PRICE;
                if( $weightObj != null ){
                    $delivery_price = $weights[$weightObj->weight];
                }

                //옵션가 사용여부
                $OptPrice = null;
            }else if($type == EasySellConstant::TYPE_DROPHUB){
                $ItemName    = $prdObj->prd_name_en;
                $prdDesc     = $prdObj->prd_desc_en_origin;
                $optionTitle = "option";
                $noticeInfo  = $prdObj->no_except_notices->pluck("attribute_value_en","attribute_name_en")->toArray();

                $images[] = $prdObj->en_main_img->img_url_origin;
                foreach ($prdObj->no_except_en_sub_imgs as $imgObj) {
                    if( $imgObj->img_url_origin ){
                        $images[] = $imgObj->img_url_origin;
                    }
                }

                //드랍허브 배송비 무료
                $delivery_price = 0;

                //옵션가 사용여부
                $OptPrice = 1;
            }

            if(count($images) < 1){
                throw new Exception("상품의 이미지가 없습니다");
            }

            $prdImgDesc = "<div><div style='width: 830px; margin:20px auto;'>
            <h5 style='text-align: center; padding: 0px; font-size: 20px; text-align: center; color: #000;font-weight: 900; margin-bottom: 40px;'>상품 이미지</h5>
            <ul style='display: flex; flex-wrap: wrap; justify-content: center;'>";
            foreach ($images as $imgUrl) {
                /** html 코드 작성 */
                $prdImgDesc .= "<li style='display: block; width: 138px; height:138px; margin:0 4px 20px 4px;'>
                    <img src='{$imgUrl}' alt='img' style='width:100%; height:100%;'>
                </li> ";
            }
            $prdImgDesc = $prdImgDesc . "</ul></div>";
            $prdDesc    = $prdImgDesc . $prdDesc . "</div>";

            $itemImage = implode("|", $images);

            $notice = getNoticeInfoTable($noticeInfo, $type);

            //상품 판매상태
            $saleStatus = EasySellConstant::STATUS_STOP_SALE;
            if( $prdObj->status == ProductConstant::PRD_STATUS_PUBLISH ){
                $saleStatus = EasySellConstant::STATUS_ON_SALE;
            }

            //옵션 설정
            $unitInfo = $optionTitle."|";
            $idx      = 0;
            foreach($prdObj->no_except_options as $option){
                //옵션명
                $replaceArr     = array("|",",","/");
                $replacementArr = array("-","\,","-");

                if($type == EasySellConstant::TYPE_W){
                    if($idx >= 50){
                        //이지셀 옵션수량 제한
                        break;
                    }

                    $optionNm = str_replace($replaceArr, $replacementArr ,$option->option_name_kr);
                    $price    = calcEasySellSalePrice($option->price_1688, $option->md_price, $delivery_price, "static", EasySellConstant::TYPE_W);
                }else if($type == EasySellConstant::TYPE_DROPHUB){
                    $optionNm = str_replace($replaceArr, $replacementArr ,$option->option_name_en);
                    $price    = calcEasySellSalePrice($option->price_1688, $option->md_price, $delivery_price, "static", EasySellConstant::TYPE_DROPHUB);
                }

                if(!$idx){
                    $buyPrice  = $price['buyPrice']; //셀러허브 공급가
                    $salePrice = $setPrice = $price['salePrice'];
                }else{
                    $unitInfo .= ",";
                }
                $setPrice = $price['salePrice'];

                if($type == EasySellConstant::TYPE_DROPHUB){
                    $buyPrice = $setPrice;
                }

                if( $option->status == ProductConstant::OPTION_SEC_ON_SALE_NUMBER ){
                    $stock = $option->amount_on_sale;
                } else {
                    $stock = 0;
                }

                //옵션구분명|옵션1^^재고^^판매가^^정가^^공급가::업체옵션번호,
                $unitInfo .= "{$optionNm}^^{$stock}^^{$setPrice}^^{$setPrice}^^{$buyPrice}::{$option->id}";

                $idx++;
            }
            // debug_log($unitInfo, "easysell/{$type}", $type);

            $voParams = [
                "ItemNo"                => $offerId,
                "ItemCategory"          => $categoryId,
                "ItemBrand"             => $ItemBrand,
                "ItemName"              => $ItemName,
                "ItemGoodCode"          => $ItemGoodCode,
                "ItemDesc"              => $ItemName,
                "ItemDescDetail"        => $prdDesc,
                "ItemGoodsRequiredDesc" => $notice,
                "ItemImage"             => $itemImage,
                "TaxYn"                 => ($prdObj->tax_type) == ProductConstant::TAX_TAXATION ? EasySellConstant::TAX_TAXATION : EasySellConstant::TAX_EXEMPTION,
                "BuyPrice"              => $buyPrice,
                "SalePrice"             => $salePrice,
                "ConsumerPrice"         => $salePrice,
                "Delfee"                => $delivery_price,
                "UnitInfo"              => $unitInfo,
                "SaleStatus"            => $saleStatus,
                "ItemMode"              => $itemMode,
                "noticeType"            => $noticeType,
                "MinEa"                 => $prdObj->start_quantity,
                "OptPrice"              => $OptPrice,
            ];

            $vo = new EasySellProductVo($type);
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
                "MinEa"                      => $vo->MinEa
            ] + $vo->ItemGoodsRequired;

            ###인코딩
            array_walk_recursive($apiParams, array($this, "_iconvArr"));

            $return = helpers_success_message($apiParams);
        } catch(Exception $e){
            $return = helpers_fail_message($e->getMessage());
        }

        return $return;
    }

    /**
     * 이지셀 고시정보 타입 매핑
     *
     * @param string $categoryId
     * @return string
     */
    private function _getNoticeType(string $categoryId): string
    {
        $defaultNotice = EasySellConstant::DEFAULT_NOTICE;

        $categoryType = substr($categoryId,0,6);
        foreach (EasySellConstant::NOTICE_MAPPING[$categoryType] as $mappingCode => $category) {
            if($mappingCode === 0){
                $defaultNotice = $category;
            }else{
                if(in_array($categoryId, $category)){
                    return $mappingCode;
                }
            }
        }
        return $defaultNotice;
    }
    /**
	 * 인코딩 변경
	 *
     * @param $item 변경할 array
	 *
     */
    private function _iconvArr (&$item)
    {
        $item = iconv("utf-8","euc-kr//TRANSLIT",$item);
    }

    /****************************************** 상품 end **********************************************/

    /****************************************** 카테고리 start **********************************************/

    /**
     * @func categoryMapping
     * @description '카테고리 매핑 저장'
     *
     * @return array
     */
    public function categoryMapping(): array
    {
        $returnMsg = $this->returnMsg;
        try {
            DB::beginTransaction();

            $filePath = public_path('app/es_categories.txt');
            if (File::exists($filePath)) {
                $lines = File::lines($filePath);
                foreach($lines as $line){
                    $data = explode(',', $line);
                    $categoryObj = CategoryMapping::where("mapping_channel", ProductConstant::MAPPING_WAPP)
                        ->where("mapping_code",$data[0])
                        ->get();
                    foreach($categoryObj as $cate){
                        //이지셀 카테고리 매핑
                        CategoryMapping::updateOrCreate([
                                "category_id"     => $cate->category_id,
                                "mapping_channel" => ProductConstant::MAPPING_ES_CHANNEL
                            ],[
                                "mapping_code" => $data[1]
                            ]);

                        //이지셀 해외카테고리 매핑
                        CategoryMapping::updateOrCreate([
                                "category_id"     => $cate->category_id,
                                "mapping_channel" => ProductConstant::MAPPING_ES_FGN_CHANNEL
                            ],[
                                "mapping_code" => $data[2]
                            ]);
                    }
                }
            } else {
                throw new Exception("파일이 존재하지 않습니다.");
            }
            $returnMsg = helpers_success_message();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func channelCateDepth
     * @description '채널 카테고리 단계 조회'
     * @param array $params
     * @return array
    */
    public function channelCateDepth(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $level     = $params["level"];
            $cate_name = $params["cate_name"];

            $cate_first  = "";
            $cate_second = "";
            $cate_third  = "";
            $cate_arr    = explode(",", $cate_name);
            $where = [];
            $group = [];
            if( $level == 1 ){
                $cate_first = $cate_arr[0];
                $where = [
                    "cate_first" => $cate_first
                ];
                $group = ["cate_second"];
            } else if( $level == 2){
                $cate_first  = $cate_arr[0];
                $cate_second = $cate_arr[1];
                $where = [
                    "cate_first"  => $cate_first,
                    "cate_second" => $cate_second,
                ];
                $group = ["cate_third"];
            } else if( $level == 3){
                $cate_first  = $cate_arr[0];
                $cate_second = $cate_arr[1];
                $cate_third  = $cate_arr[2];
                $where = [
                    "cate_first"  => $cate_first,
                    "cate_second" => $cate_second,
                    "cate_third"  => $cate_third,
                ];
                $group = ["cate_fourth"];
            }

            $nextCateObjs = SellerhubCategory::where($where)
            ->orderBy("cate_first", "asc")
            ->orderBy("cate_second", "asc")
            ->orderBy("cate_third", "asc")
            ->orderBy("cate_fourth", "asc")
            ->groupBy($group)->get();

            $data = [];
            foreach ($nextCateObjs as $nextCateObj) {
                $data[] = [
                    "cate_first"  => $nextCateObj->cate_first,
                    "cate_second" => $nextCateObj->cate_second,
                    "cate_third"  => $nextCateObj->cate_third,
                    "cate_fourth" => $nextCateObj->cate_fourth,
                ];
            }
            $returnMsg = helpers_success_message($data);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }
        return $returnMsg;
    }

    /**
     * @func channelCateList
     * @description '채널 카테고리 목록 조회'
     * @param array $params
     * @return array
    */
    public function channelCateList(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $keyword     = $params["keyword"];
            $cate_second = $params["cate_second"];
            $cate_third  = $params["cate_third"];
            $cate_fourth = $params["cate_fourth"];

            $builder = SellerhubCategory::query();
            $builder->where("cate_first", EasySellConstant::DEFAULT_CATEGORY);

            if( $cate_second != "" ){
                $builder->where("cate_second", $cate_second);
            }
            if( $cate_third != "" ){
                $builder->where("cate_third", $cate_third);
            }
            if( $cate_fourth != "" ){
                $builder->where("cate_fourth", $cate_fourth);
            }
            if( $keyword != "" ){
                $builder->where(function($query) use ($keyword) {
                    $query->where("cate_first", "like", "%" . $keyword . "%")
                        ->orWhere("cate_second", "like", "%" . $keyword . "%")
                        ->orWhere("cate_third", "like", "%" . $keyword . "%")
                        ->orWhere("cate_fourth", "like", "%" . $keyword . "%");
                });
            }

            $result = $builder->get();

            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }
        return $returnMsg;
    }

    /****************************************** 카테고리 end **********************************************/

    /****************************************** 이미지 start **********************************************/

    /**
     * @func imgCallBack
     * @description '이미지 콜백'
     * @param array $params
     * @return array
    */
    public function imgCallBack(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {

            $returnMsg = helpers_success_message();

        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /****************************************** 이미지 end **********************************************/

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
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
