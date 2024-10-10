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
use App\Models\OrderBaseData;
use App\Models\OrderChannelData;
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
                $productShno = "444";

                $inFailObj = BonaeraInFailData::where("order_id", $orderId)->first();
                if( $inFailObj !== null ){
                    if( empty($inFailObj->hs_code ) ){
                        throw new Exception(BonaeraErrorMessageConstant::getNotHaveErrorMessage("HS_CODE"));
                    }
                    $productShno = $inFailObj->hs_code;
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
            } else {
                throw new Exception(OrderErrorMessageConstant::getHaveErrorMessage("BASE_INFO"));
            }
        } catch (Exception $e) {
            $erroMsg = "error: " . $e->getMessage();
            BonaeraInFailData::updateOrCreate(
                [
                    "order_id" => $orderId,
                ],
                [
                    "msg" => $erroMsg
                ]
            );
            debug_log($erroMsg . " | order_id: " . $orderId, "boneara/createStockApi", "createStockApi");
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

    /** 신청소 조회 */
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
