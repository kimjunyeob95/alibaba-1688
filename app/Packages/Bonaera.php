<?php

namespace App\Packages;

use App\Constants\BonaeraConstant;
use App\Constants\BonaeraErrorMessageConstant;
use App\Constants\ImageConstant;
use App\Constants\ImageErrorMessageConstant;
use App\Constants\OrderErrorMessageConstant;
use App\Constants\ProductConstant;
use App\Constants\ProductErrorMessageConstant;
use App\Http\Request\Bonaera\BonaeraOutDeliveryUpdateRequest;
use App\Models\BonaeraInBaseData;
use App\Models\BonaeraInFailData;
use App\Models\BonaeraInProductData;
use App\Models\BonaeraOutBaseData;
use App\Models\BonaeraOutFailData;
use App\Models\BonaeraOutProductData;
use App\Models\HsCodeData;
use App\Models\OrderBaseData;
use App\Models\OrderChannelData;
use App\Models\OrderChannelDetailData;
use App\Models\OrderLogisticsData;
use App\Models\OrderProductData;
use App\Models\ProductData;
use App\Models\ProductImageData;
use App\Models\ProductOptionData;
use App\Vo\Bonaera\BonaeraStockModifyApiDto;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class Bonaera
{
    private array $returnMsg;
    private string $domain;
    private array $header;
    private string $userId;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
        $this->domain    = "https://bonaera.com";
        $this->header    = [
            'userKey: ' . env("BONAERA_TOKEN" , "DdBGRBW3IAJo6a53gviV3e9dr3645SSOe4oRzEOerlvt6tJeqn1qxJG1MFxTKqDG"),
            'Content-Type: application/json'
        ];
        $this->userId = env("BONAERA_USER_ID" , "sellerhub");
    }

    /** 입고신청 */
    public function createStockApi(string $orderId): array
    {
        $returnMsg = $this->returnMsg;
        $endPoint  = $this->domain . '/elpisapi/stock_api.php';
    
        try {
            $inBaseObj = BonaeraInBaseData::where("order_id", $orderId)->first();

            if( $inBaseObj === null ){
                $orderObj = OrderBaseData::where("order_id", $orderId)->first();
                if( $orderObj == null ){
                    throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER"));
                }
                $orderChannelObj = OrderChannelData::where("order_id", $orderId)->first();
                if( $orderChannelObj == null ){
                    throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_CHANNEL"));   
                }
                $offerId = $orderObj->offer_id;
                $prdObj  = ProductData::where("offer_id", $offerId)->first();
                if( $prdObj == null ){
                    throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("PRODUCT"));
                }
                $orderPrdObjs = OrderProductData::where("order_id", $orderId)->get();
                $imgObj       = ProductImageData::where("offer_id", $offerId)->where([
                    "img_type" => ImageConstant::IMAGE_TYPE_MAIN,
                    "lang"     => ProductConstant::COLLECT_KR
                ])->first();
                
                $itemList    = [];
                $optList     = [];
                $productShno = "";

                $inFailObj = BonaeraInFailData::where("order_id", $orderId)->first();
                if( $inFailObj !== null ){
                    if( empty($inFailObj->hs_code ) ){
                        throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("HS_CODE"));
                    }

                    $hsCodeObj   = HsCodeData::where("hs_code", $inFailObj->hs_code)->first();
                    $productShno = $hsCodeObj->sh_no ?? "";
                }

                if( empty($productShno) ){
                    throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("PRODUCTSHNO"));
                }

                $hsCodeObj = HsCodeData::where("sh_no", $productShno)->first();
                if( $hsCodeObj === null ){
                    throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("HS_CODE"));
                }
                
                foreach ($orderPrdObjs as $orderPrdObj) {
                    $subItemId       = $orderPrdObj->sub_item_id;
                    $logisticsBillNo = "트레킹 번호 없음";
                    $logicObj        = OrderLogisticsData::where([
                        "order_id" => $orderId,
                    ])->where('sub_item_ids', 'LIKE', '%' . $subItemId . '%')->first();

                    if( $logicObj !== null ){
                        $logisticsBillNo = $logicObj->logistics_bill_no;
                    }

                    $skuId  = $orderPrdObj->sku_id;
                    $optObj = ProductOptionData::where("offer_id", $offerId)->where("sku_id", $skuId)->first();

                    if( $optObj !== null ){
                        $sku_img_url = $optObj->sku_img_url;
        
                        $itemList[] = [
                            "productShno"    => $productShno,
                            "productNameEng" => $prdObj->prd_name_kr,
                            "trackingNumber" => $logisticsBillNo,
                            "productMoney"   => $orderPrdObj->price,
                            "productCount"   => $orderPrdObj->quantity,
                            "imgUrl"         => !empty($sku_img_url) ? $sku_img_url : $imgObj->img_url_origin,
                            "option1"        => $optObj->option_name_kr,
                            "option2"        => $optObj->id,
                        ];
                        $optList[] = [
                            "option_id"            => $optObj->id,
                            "quantity"             => $orderPrdObj->quantity,
                            "product_snapshot_url" => $orderPrdObj->product_snapshot_url,
                            "hs_code"              => $hsCodeObj->hs_code
                        ];
                    }
                }

                if( !empty($itemList) ){
                    $payload  = [
                        "userId"    => $this->userId,
                        "orderMemo" => $orderChannelObj->buyer_memo,
                        "itemList"  => $itemList
                    ];
        
                    $result = helpers_curl("POST", $endPoint, $this->header, $payload);
                    if( isset($result["stockNo"]) && isset($result["item"]) && !empty($result["item"]) ){

                        $stockNo = $result["stockNo"];

                        try {

                            DB::beginTransaction();
    
                            BonaeraInBaseData::updateOrCreate(
                                [
                                    "stock_no" => $stockNo,
                                    "order_id" => $orderId,
                                ],
                                [
                                    "offer_id" => $offerId
                                ]
                            );
                
                            foreach ($result["item"] as $key => $item) {
                                $itCode = $item["itCode"];
                                $opt    = $optList[$key];
                
                                BonaeraInProductData::updateOrCreate(
                                    [
                                        "stock_no"  => $stockNo,
                                        "order_id"  => $orderId,
                                        "option_id" => $opt["option_id"],
                                    ],
                                    [
                                        "quantity"             => $opt["quantity"],
                                        "product_snapshot_url" => $opt["product_snapshot_url"],
                                        "hs_code"              => $opt["hs_code"],
                                        "it_Code"              => $itCode,
                                        "status"               => BonaeraConstant::WAREHOUSE_STATUS_PENDING,
                                        "received_qty"         => 0,
                                        "discarded_qty"        => 0,
                                        "refunded_qty"         => 0,
                                        "shipped_qty"          => 0,
                                        "lack_status"          => BonaeraConstant::LACK_STATUS_N,
                                        "stock_qty"            => 0,
                                        "memo"                 => "",
                                    ]
                                );
                            }

                            BonaeraInFailData::where("order_id", $orderId)->forceDelete();
                            
                            DB::commit();

                            $logMessage = [
                                "name"     => "입고신청",
                                "order_id" => $orderId,
                                "result"   => "성공",
                                "payload"  => $payload,
                            ];
                            debug_log(json_encode($logMessage, JSON_UNESCAPED_UNICODE), "boneara/log-jisong", "createStockApi");

                            $returnMsg = helpers_success_message(["stock_no" => $stockNo]);
                        } catch (Exception $dbError) {
                            DB::rollBack();
                            throw new Exception($dbError->getMessage());   
                        }
                    } else {
                        $msg = "보내라 입고신청 API 에러";
                        if( isset($result["message"]) ){
                            $msg = $result["message"];
                        }
                        throw new Exception($msg);
                    }
                } else {
                    throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("ITEMLIST"));
                }
            }
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();

            if( BonaeraInFailData::where("order_id", $orderId)->exists() ){
                BonaeraInFailData::where("order_id", $orderId)->update([
                    "msg" => $errorMsg
                ]);
            } else {
                BonaeraInFailData::create([
                    "order_id" => $orderId,
                    "hs_code"  => "",
                    "msg"      => $errorMsg
                ]);
            }
            $logMessage = [
                "name"     => "입고신청",
                "order_id" => $orderId,
                "result"   => "실패",
                "error"    => $errorMsg,
            ];
            debug_log(json_encode($logMessage, JSON_UNESCAPED_UNICODE), "boneara/log-jisong", "createStockApi");

            $returnMsg = helpers_fail_message($errorMsg);
        }

        return $returnMsg;
    }

    /** 재고현황 조회 */
    public function getStockList(string $stockCode, string $itCode = null): array
    {
        $returnMsg = $this->returnMsg;
        
        $endPoint = $this->domain . '/elpisapi/stockList_api.php';
        $payload  = [
            "userId" => $this->userId,
            "stCode" => $stockCode,
        ];

        if( $itCode != null ){
            $payload["itCode"] = $itCode;
        }

        try {
            $result = helpers_curl("GET", $endPoint, $this->header, $payload);
            if( !isset($result["message"]) || $result["message"] != "success" || !isset($result["data"]["appList"][0]) ) {
                throw new Exception("stockList_api 통신");
            }

            $returnMsg = helpers_success_message($result["data"]["appList"][0]);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /** 출고신청 */
    public function createApplicationApi(string $orderId): array
    {
        $returnMsg = $this->returnMsg;
        $endPoint  = $this->domain . '/elpisapi/application_api.php';
    
        try {
            $orderBaseObj     = OrderBaseData::where("order_id", $orderId)->first();
            $orderChannelObjs = OrderChannelData::where("order_id", $orderId)->get();
            $inBaseObj        = BonaeraInBaseData::where("order_id", $orderId)->first();
            $outBaseObj       = BonaeraOutBaseData::where("order_id", $orderId)->first();
            $inPrdObjs        = BonaeraInProductData::where("order_id", $orderId)->get();

            if( $orderBaseObj === null ){
                throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDERBASE"));
            }
            if( $inBaseObj === null ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_IN_BASE_DATA"));
            }
            if( count($orderChannelObjs) < 1 ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("ORDERCHANNELOBJS"));
            }
            if( count($inPrdObjs) < 1 ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_IN_PRODUCT_DATA"));
            }
            $inStatus = true;
            foreach ($inPrdObjs as $inPrdObj) {
                if( $inPrdObj->status !== BonaeraConstant::WAREHOUSE_STATUS_RECEIVED ){
                    $inStatus = false;
                    break;
                }
            }
            if( $inStatus !== true ){
                throw new Exception(BonaeraErrorMessageConstant::getFitErrorMessage("ALL_OPTION_NOT_READY"));
            }

            if( $outBaseObj === null ){
                foreach ($orderChannelObjs as $orderChannelObj) {
                    $itemList = [];
                    $optList  = [];
        
                    $orderId        = $orderChannelObj->order_id;
                    $channelOrderId = $orderChannelObj->channel_order_id;
                    $stockNo        = $inBaseObj->stock_no;
        
                    foreach ($inPrdObjs as $inPrdObj) {
                        $itCode = $inPrdObj->it_code;
        
                        $orderChannnelDetailObj = OrderChannelDetailData::where([
                            "order_channel_id" => $orderChannelObj->id,
                            "option_id"        => $inPrdObj->option_id,
                        ])->first();
                        
                        if( $orderChannnelDetailObj === null ){
                            throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_CHANNEL_DETAIL_DATAS"));
                        }

                        $quantity = $orderChannnelDetailObj->quantity;
        
                        $itemList[] = [
                            "stockitemCode" => $itCode,
                            "orderNumber"   => $channelOrderId,
                            "productCount"  => $orderChannnelDetailObj->quantity,
                            "siteUrl"       => $inPrdObj->product_snapshot_url,
                            "localFee"      => $orderBaseObj->shipping_fee
                        ];
                        $optList[] = [
                            "option_id" => $inPrdObj->option_id,
                            "it_code"   => $itCode,
                            "quantity"  => $quantity,
                        ];
                    }

                    if( empty($itemList) ){
                        throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("ITEMLIST"));
                    }
                    if( empty($optList) ){
                        throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("OPTLIST"));
                    }
        
                    $payload  = [
                        "userId"  => $this->userId,
                        "ctrNum"  => BonaeraConstant::SHIPPING_TYPE[$orderChannelObj->shipping_type],
                        "RecInfo" => [
                            [
                                "receiverName"  => $orderChannelObj->buyer_name,
                                "zipCode"       => $orderChannelObj->buyer_zipcode,
                                "addr1"         => $orderChannelObj->buyer_address,
                                "addr2"         => "",
                                "receiverPhone" => $orderChannelObj->buyer_number,
                                "personalNum"   => $orderChannelObj->buyer_clearance_number,
                                "shipMemo"      => $orderChannelObj->buyer_memo,
                                "personalType"  => BonaeraConstant::CLEARANCE_TYPE_VARCHAR[$orderChannelObj->clearance_type],
                            ]
                        ],
                        "itemList" => $itemList
                    ];
        
                    $result = helpers_curl("POST", $endPoint, $this->header, $payload);

                    if( isset($result["groupNo"]) && isset($result["orderNo"]) && isset($result["invoice"]) ){
                        $groupNo = $result["groupNo"];

                        try {
                            DB::beginTransaction();

                            $outBaseObj = BonaeraOutBaseData::updateOrCreate(
                                [
                                    "sh_no" => $result["orderNo"],
                                ],
                                [
                                    "stock_no"         => $stockNo,
                                    "order_id"         => $orderId,
                                    "channel_order_id" => $channelOrderId,
                                    "state"            => BonaeraConstant::GROUP_STATUS_302,
                                    "group_no"         => $groupNo,
                                    "out_ordered_at"   => Carbon::now(),
                                    "out_completed_at" => null,
                                ]
                            );
            
                            foreach ($optList as $opt) {
                                BonaeraOutProductData::updateOrCreate(
                                    [
                                        "sh_no"     => $result["orderNo"],
                                        "option_id" => $opt["option_id"],
                                        "it_code"   => $opt["it_code"],
                                    ],
                                    [
                                        "channel_order_id" => $channelOrderId,
                                        "quantity"         => $opt["quantity"],
                                        "shipped_qty"      => 0,
                                    ]
                                );
                            }
                            DB::commit();

                            $returnMsg = helpers_success_message();
                        } catch (Exception $dbError) {
                            DB::rollBack();
                            throw new Exception($dbError->getMessage());   
                        }
                    } else {
                        $msg = "보내라 출고신청 API 에러";
                        if( isset($result["message"]) ){
                            $msg = $result["message"];
                        }
                        throw new Exception($msg);
                    }
                }
                
            }
        } catch (Throwable $e) {
            $errorMsg = $e->getMessage();

            BonaeraOutFailData::updateOrCreate(
                [
                    "order_id" => $orderId,
                ],
                [
                    "msg" => $errorMsg
                ]
            );
            // debug_log($errorMsg . " | order_id: " . $orderId, "boneara/createApplicationApi", "createApplicationApi");

            $returnMsg = helpers_fail_message($errorMsg);
        }

        return $returnMsg;
    }

    /** 신청서 조회 */
    public function getApplicationList(string $groupNo): array
    {
        $returnMsg = $this->returnMsg;
        
        $endPoint = $this->domain . '/elpisapi/applicationList_api.php';
        $payload  = [
            "userId" => $this->userId,
            "grCode" => $groupNo,
        ];

        try {
            $result = helpers_curl("GET", $endPoint, $this->header, $payload);
            if( !isset($result["message"]) || $result["message"] != "success" || !isset($result["data"]["ReciverInfo"][0]) ) {
                throw new Exception("applicationList_api 통신");
            }

            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /** 상품정보조회(출고신청번호기준) 조회 */
    public function getOrderApplicationList(string $shNo): array
    {
        $returnMsg = $this->returnMsg;
        
        $endPoint = $this->domain . '/elpisapi/orderApplicationList_api.php';
        $payload  = [
            "userId" => $this->userId,
            "orCode" => $shNo,
        ];

        try {
            $result = helpers_curl("GET", $endPoint, $this->header, $payload);
            if( !isset($result["message"]) || $result["message"] != "success" || !isset($result["data"]) ) {
                throw new Exception("orderApplicationList_api 통신");
            }

            $returnMsg = helpers_success_message($result["data"]);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /** 배송비 결제 */
    public function getPayment(string $groupNo): array
    {
        $returnMsg = $this->returnMsg;
        
        $endPoint = $this->domain . '/elpisapi/payment_api.php';
        $payload  = [
            "userId"  => $this->userId,
            "groupNo" => $groupNo,
        ];

        try {
            $result = helpers_curl("POST", $endPoint, $this->header, $payload);
            if( !isset($result["groupNo"]) || empty($result["groupNo"]) ) {
                $errorMsg = "payment_api 통신";
                if( isset($result["message"]) && $result["message"] ){
                    $errorMsg = $result["message"];
                }
                throw new Exception($errorMsg);
            }

            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /** 재고신청서 수정 */
    public function stockModifyApi(string $stockNo): array
    {
        $returnMsg = $this->returnMsg;
        
        $endPoint = $this->domain . '/elpisapi/stockModify_api.php';
        $payload  = [
            "userId"  => $this->userId,
            "stockNo" => $stockNo
        ];

        try {
            $inBaseObj = BonaeraInBaseData::where("stock_no", $stockNo)->first();
            if( $inBaseObj === null ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_IN_BASE_DATA"));
            }
            $prdObj = ProductData::where("offer_id", $inBaseObj->offer_id)->first();
            if( $prdObj === null ){
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT"));
            }
    
            $itemList = [];
            $inProductObjs = BonaeraInProductData::with(["w_option"])->where("stock_no", $stockNo)->get();
            foreach ($inProductObjs as $inProductObj) {
                if( $inProductObj->w_option ){
                    $hsCodeObj   = HsCodeData::where("hs_code", $inProductObj->hs_code)->first();
                    $productShno = $hsCodeObj->sh_no ?? "";
                    $offerId     = $inBaseObj->offer_id;

                    $orderPrdObj = OrderProductData::where([
                        "order_id" => $inProductObj->order_id,
                        "offer_id" => $offerId,
                    ])->first();
                    if( $orderPrdObj === null ){
                        throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_PRODUCT_DATAS"));
                    }

                    $imgObj = ProductImageData::where("offer_id", $offerId)->where([
                        "img_type" => ImageConstant::IMAGE_TYPE_MAIN,
                        "lang"     => ProductConstant::COLLECT_KR
                    ])->first();
                    if( $imgObj === null ){
                        throw new Exception(ImageErrorMessageConstant::getNotHaveErrorMessage("IMAGE"));
                    }

                    $skuId  = $orderPrdObj->sku_id;
                    $optObj = ProductOptionData::where("offer_id", $offerId)->where("sku_id", $skuId)->first();
                    if( $optObj === null ){
                        throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("OPTION"));
                    }
                    $sku_img_url = $optObj->sku_img_url;

                    $logisticsBillNo = "트레킹 번호 없음";
                    $logicObj        = OrderLogisticsData::where([
                        "order_id" => $inProductObj->order_id,
                    ])->where('sub_item_ids', 'LIKE', '%' . $orderPrdObj->sub_item_id . '%')->first();
                    if( $logicObj !== null ){
                        $logisticsBillNo = $logicObj->logistics_bill_no;
                    }

                    $itemList[] = [
                        "itCode"         => $inProductObj->it_code,
                        "productShno"    => $productShno,
                        "productNameEng" => $prdObj->prd_name_kr,
                        "trackingNumber" => $logisticsBillNo,
                        "productMoney"   => $orderPrdObj->price,
                        "productCount"   => $orderPrdObj->quantity,
                        "imgUrl"         => !empty($sku_img_url) ? $sku_img_url : $imgObj->img_url_origin,
                        "option1"        => $optObj->option_name_kr,
                        "option2"        => $optObj->id,
                    ];
                }
            }

            if( !empty($itemList) ){
                $payload["itemList"] = $itemList;
            }

            $result = helpers_curl("PUT", $endPoint, $this->header, $payload);
            if( !isset($result["message"]) || $result["message"] !== "재고수정완료" || empty($result["stockNo"]) ) {
                throw new Exception("stockModify_api 통신");
            }

            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $errorMsg  = "error: " . $e->getMessage();
            $returnMsg = helpers_fail_message($e->getMessage());
            debug_log($errorMsg . " | stockNo: " . $stockNo, "boneara/stockModifyApi", "stockModifyApi");
        }

        return $returnMsg;
    }

    /**
     * @func stockModifyApiBindCall
     * @description '재고신청서 수정'
     * @param string $orderId
     * @param array $bonaeraStockModifyApiDtos BonaeraStockModifyApiDto
     * @param string $code
     * @return array $returnMsg
     */
    public function stockModifyApiBindCall(string $orderId, array $bonaeraStockModifyApiDtos, string $code = ""): array
    {
        $returnMsg = $this->returnMsg;
        $endPoint  = $this->domain . '/elpisapi/stockModify_api.php';

        
        try {
            if( empty($bonaeraStockModifyApiDtos) ) {
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_STOCK_MODIFY_API_DTOS"));
            }

            $inBaseObj = BonaeraInBaseData::where("order_id", $orderId)->first();
            if( $inBaseObj === null ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_IN_BASE_DATA"));
            }

            $payload  = [
                "userId"   => $this->userId,
                "stockNo"  => $inBaseObj->stock_no,
                "itemList" => $bonaeraStockModifyApiDtos
            ];

            $result = helpers_curl("PUT", $endPoint, $this->header, $payload);
            if( !isset($result["message"]) || $result["message"] !== "재고수정완료" || empty($result["stockNo"]) ) {
                throw new Exception(BonaeraErrorMessageConstant::getFitErrorMessage("STOCKMODIFY_API"));
            }

            $returnMsg = helpers_success_message($result);

            $logMessage = [
                "name"     => "재고신청서 수정",
                "order_id" => $orderId,
                "code"     => $code,
                "result"   => "성공",
                "payload"  => $payload,
            ];
            debug_log(json_encode($logMessage, JSON_UNESCAPED_UNICODE), "boneara/log-jisong", "stockModifyApiBindCall");
        } catch (Throwable $e) {
            $returnMsg = helpers_fail_message($e->getMessage());

            $logMessage = [
                "name"     => "재고신청서 수정",
                "order_id" => $orderId,
                "code"     => $code,
                "result"   => "실패",
                "error"    => $e->getMessage(),
            ];
            debug_log(json_encode($logMessage, JSON_UNESCAPED_UNICODE), "boneara/log-jisong", "stockModifyApiBindCall");
        }

        return $returnMsg;
    }

    /**
     * @func stockModifyApiBindOT002
     * @description '재고신청서 수정(OT002)'
     * @param string $orderId
     * @param string $logisticsCode 'params1'
     * @return array bonaeraStockModifyApiDtos
     */
    public function stockModifyApiBindOT002(string $orderId, string $logisticsCode): array
    {
        $bonaeraStockModifyApiDtos = [];

        try {
            if( empty($orderId) ){
                throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_ID"));
            }
            if( empty($logisticsCode) ){
                throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("LOGISTICSCODE"));
            }
            
            $logicObj = OrderLogisticsData::where([
                "order_id"       => $orderId,
                "logistics_code" => $logisticsCode,
            ])->first();
            if( $logicObj === null ){
                throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_LOGISTICS_DATAS"));
            }

            $logisticsBillNo = $logicObj->logistics_bill_no;
            $subItemIds      = explode(",", $logicObj->sub_item_ids) ?? [];

            if( empty($subItemIds) ){
                throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("SUB_ITEM_IDS"));
            }

            foreach ($subItemIds as $subItemId) {
                $orderPrdObj = OrderProductData::where([
                    "order_id"    => $orderId,
                    "sub_item_id" => $subItemId,
                ])->first();
                if( $orderPrdObj === null ){
                    throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_PRODUCT_DATAS"));
                }

                $offerId = $orderPrdObj->offer_id;
                $skuId   = $orderPrdObj->sku_id;

                $prdObj = ProductData::where("offer_id", $offerId)->first();
                if( $prdObj === null ){
                    throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT"));
                }

                $optObj = ProductOptionData::where("offer_id", $offerId)->where("sku_id", $skuId)->first();
                if( $optObj === null ){
                    throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("OPTION"));
                }

                $imgObj = ProductImageData::where("offer_id", $offerId)->where([
                    "img_type" => ImageConstant::IMAGE_TYPE_MAIN,
                    "lang"     => ProductConstant::COLLECT_KR
                ])->first();
                if( $imgObj === null ){
                    throw new Exception(ImageErrorMessageConstant::getNotHaveErrorMessage("IMAGE"));
                }

                $inPrdObj = BonaeraInProductData::where([
                    "order_id"  => $orderId,
                    "option_id" => $optObj->id,
                ])->first();
                if( $inPrdObj === null ){
                    throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_IN_PRODUCT_DATA"));
                }

                if( $inPrdObj->status !== BonaeraConstant::WAREHOUSE_STATUS_PENDING ) continue;

                $hsCodeObj   = HsCodeData::where("hs_code", $inPrdObj->hs_code)->first();
                $productShno = $hsCodeObj->sh_no ?? "";

                $bonaeraStockModifyApiDtoBind = [
                    'itCode'         => $inPrdObj->it_code,
                    'productShno'    => $productShno,
                    'productNameEng' => $prdObj->prd_name_kr,
                    'trackingNumber' => $logisticsBillNo,
                    'productMoney'   => $orderPrdObj->price,
                    'productCount'   => $orderPrdObj->quantity,
                    'imgUrl'         => !empty($sku_img_url) ? $sku_img_url : $imgObj->img_url_origin,
                    'option1'        => $optObj->option_name_kr,
                    'option2'        => $optObj->id,
                ];
                $bonaeraStockModifyApiDto = new BonaeraStockModifyApiDto();
                $bonaeraStockModifyApiDto->bind($bonaeraStockModifyApiDtoBind);

                $bonaeraStockModifyApiDtos[] = $bonaeraStockModifyApiDto->getAllProperties();
            }
        } catch (Throwable $e) {
            debug_log("error: " . $e->getMessage() . " | orderId: " . $orderId . " | logisticsCode: " . $logisticsCode, "boneara/stockModifyApi", "stockModifyApiBindOT002");
        }

        return $bonaeraStockModifyApiDtos;
    }

     /**
     * @func stockModifyApiBindOS002
     * @description '재고신청서 수정(OS002)'
     * @param string $orderId
     * @return array bonaeraStockModifyApiDtos
     */
    public function stockModifyApiBindOS002(string $orderId): array
    {
        $bonaeraStockModifyApiDtos = [];

        try {
            if( empty($orderId) ){
                throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_ID"));
            }

            $inPrdObjs = BonaeraInProductData::where([
                "order_id" => $orderId,
                "status" => BonaeraConstant::WAREHOUSE_STATUS_PENDING,
            ])->get();

            if( count($inPrdObjs) < 1 ){
                throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("BONAERA_IN_PRODUCT_DATA"));
            }

            foreach ($inPrdObjs as $inPrdObj) {
                $optObj = ProductOptionData::where("id", $inPrdObj->option_id)->first();
                if( $optObj === null ){
                    throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("OPTION"));
                }

                $offerId = $optObj->offer_id;
                $prdObj  = ProductData::where("offer_id", $offerId)->first();
                if( $prdObj === null ){
                    throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT"));
                }

                $orderPrdObj = OrderProductData::where([
                    "order_id" => $orderId,
                    "sku_id"   => $optObj->sku_id,
                ])->first();
                if( $orderPrdObj === null ){
                    throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_PRODUCT_DATAS"));
                }

                $subItemId   = $orderPrdObj->sub_item_id;
                $hsCodeObj   = HsCodeData::where("hs_code", $inPrdObj->hs_code)->first();
                $productShno = $hsCodeObj->sh_no ?? "";

                $logicObj = OrderLogisticsData::where([
                    "order_id" => $orderId,
                ])->where('sub_item_ids', 'LIKE', '%' . $subItemId . '%')->first();
                if( $logicObj === null ){
                    throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_LOGISTICS_DATAS"));
                }
    
                $logisticsBillNo = $logicObj->logistics_bill_no;

                $imgObj = ProductImageData::where("offer_id", $offerId)->where([
                    "img_type" => ImageConstant::IMAGE_TYPE_MAIN,
                    "lang"     => ProductConstant::COLLECT_KR
                ])->first();
                if( $imgObj === null ){
                    throw new Exception(ImageErrorMessageConstant::getNotHaveErrorMessage("IMAGE"));
                }

                $bonaeraStockModifyApiDtoBind = [
                    'itCode'         => $inPrdObj->it_code,
                    'productShno'    => $productShno,
                    'productNameEng' => $prdObj->prd_name_kr,
                    'trackingNumber' => $logisticsBillNo,
                    'productMoney'   => $orderPrdObj->price,
                    'productCount'   => $orderPrdObj->quantity,
                    'imgUrl'         => !empty($sku_img_url) ? $sku_img_url : $imgObj->img_url_origin,
                    'option1'        => $optObj->option_name_kr,
                    'option2'        => $optObj->id,
                ];
                $bonaeraStockModifyApiDto = new BonaeraStockModifyApiDto();
                $bonaeraStockModifyApiDto->bind($bonaeraStockModifyApiDtoBind);

                $bonaeraStockModifyApiDtos[] = $bonaeraStockModifyApiDto->getAllProperties();
            }
        } catch (Throwable $e) {
            debug_log("error: " . $e->getMessage() . " | orderId: " . $orderId, "boneara/stockModifyApi", "stockModifyApiBindOS002");
        }

        return $bonaeraStockModifyApiDtos;
    }

    /** 신청서 수정 */
     /**
     * @func applicationModifyApi
     * @description '신청서 수정'
     * @param BonaeraOutDeliveryUpdateRequest $request
     * @return array
     */
    public function applicationModifyApi(BonaeraOutDeliveryUpdateRequest $request): array
    {
        $returnMsg = $this->returnMsg;
        $endPoint  = $this->domain . '/elpisapi/applicationModify_api.php';
        
        try {
            $payload  = [
                "userId"  => $this->userId,
                "groupNo" => $request->groupNo,
                "ctrNum"  => $request->ctrNum,
                "RecInfo" => [
                    [
                        "receiverName"  => $request->receiverName,
                        "zipCode"       => $request->zipCode,
                        "addr1"         => $request->addr1,
                        "addr2"         => "",
                        "receiverPhone" => $request->receiverPhone,
                        "personalType"  => $request->personalType,
                        "personalNum"   => $request->personalNum,
                        "shipMemo"      => $request->shipMemo,
                    ]
                ],
            ];

            $result = helpers_curl("PUT", $endPoint, $this->header, $payload);
            if( !isset($result["message"]) || $result["message"] !== "수정완료" ) {
                $errorMsg = "applicationModify_api 통신";
                if( !empty($result["message"]) ){
                    $errorMsg = $result["message"];
                }
                throw new Exception($errorMsg);
            }

            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $errorMsg  = "error: " . $e->getMessage();
            $returnMsg = helpers_fail_message($e->getMessage());
            // debug_log($errorMsg . " | groupNo: " . $request->groupNo, "boneara/applicationModifyApi", "applicationModifyApi");
        }

        return $returnMsg;
    }
}
