<?php

namespace App\Packages;

use App\Constants\BonaeraConstant;
use App\Constants\BonaeraErrorMessageConstant;
use App\Constants\ImageConstant;
use App\Constants\OrderErrorMessageConstant;
use App\Constants\ProductConstant;
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
use Exception;
use Illuminate\Support\Facades\DB;

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
            'userKey: ' . env("BONAERA_TOKEN" , "3dI7uzN1dERvCBM1wt9wp1CglC7hcBB0jFkLZAFjZDC7SP56TIfwcJhfpTbLCIjg"),
            'Content-Type: application/json'
        ];
        $this->userId = BonaeraConstant::USER_ID;
    }

    /** 입고신청 */
    public function createStockApi(string $orderId): void
    {
        $endPoint = $this->domain . '/elpisapi/stock_api.php';
    
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
                $orderPrdObjs     = OrderProductData::where("order_id", $orderId)->get();
                $logicObjs        = OrderLogisticsData::where("order_id", $orderId)->groupBy("logistics_bill_no")->get();
                $logisticsBillNos = $logicObjs->pluck('logistics_bill_no')->filter()->implode(',');
                if( empty($logisticsBillNos) ){
                    throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("LOGISTICS_BILL_NO"));
                }
                $imgObj = ProductImageData::where("offer_id", $offerId)->where([
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
                
                foreach ($orderPrdObjs as $orderPrdObj) {
                    $skuId  = $orderPrdObj->sku_id;
                    $optObj = ProductOptionData::where("offer_id", $offerId)->where("sku_id", $skuId)->first();

                    if( $optObj !== null ){
                        $sku_img_url = $optObj->sku_img_url;
        
                        $itemList[] = [
                            "productShno"    => $productShno,
                            "productNameEng" => $prdObj->prd_name_kr,
                            "trackingNumber" => $logisticsBillNos,
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
                            "hs_code"              => $productShno
                        ];
                    }
                }

                if( !empty($itemList) ){
                    $payload  = [
                        "userId"    => BonaeraConstant::USER_ID,
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
            // debug_log($errorMsg . " | order_id: " . $orderId, "boneara/createStockApi", "createStockApi");
        }
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
    public function createApplicationApi(string $orderId): void
    {
        $endPoint = $this->domain . '/elpisapi/application_api.php';
    
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
                        "userId"  => BonaeraConstant::USER_ID,
                        "ctrNum"  => 2,
                        "RecInfo" => [
                            [
                                "receiverName"  => $orderChannelObj->buyer_name,
                                "zipCode"       => $orderChannelObj->buyer_zipcode,
                                "addr1"         => $orderChannelObj->buyer_address,
                                "addr2"         => "",
                                "receiverPhone" => $orderChannelObj->buyer_number,
                                "personalNum"   => $orderChannelObj->buyer_clearance_number,
                                "shipMemo"      => $orderChannelObj->buyer_memo,
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
                                    "group_no"         => $groupNo,
                                    "out_ordered_at"   => null,
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
        } catch (Exception $e) {
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
        }
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
}
