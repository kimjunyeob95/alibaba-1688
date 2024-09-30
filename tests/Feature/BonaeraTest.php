<?php

namespace Tests\Feature;

use App\Constants\BonaeraConstant;
use App\Models\BonaeraInBaseData;
use App\Models\BonaeraInProductData;
use App\Models\BonaeraOutBaseData;
use App\Models\BonaeraOutDeliveryData;
use App\Models\BonaeraOutProductData;
use App\Models\BonaeraOutWeightData;
use App\Models\OrderBaseData;
use App\Models\OrderChannelData;
use App\Models\OrderChannelDetailData;
use App\Models\OrderLogisticsData;
use App\Models\OrderProductData;
use App\Models\ProductData;
use App\Models\ProductImageData;
use App\Models\ProductOptionData;
use Tests\TestCase;

class BonaeraTest extends TestCase
{
    # php artisan test --filter testCreateWarehouse
    /** 입고신청 */
    public function testCreateWarehouse()
    {
        $endPoint = "https://bonaera.com/elpisapi/stock_api.php";
        $header   = [
            'userKey: ' . env("BONAERA_TOKEN" , "3dI7uzN1dERvCBM1wt9wp1CglC7hcBB0jFkLZAFjZDC7SP56TIfwcJhfpTbLCIjg"),
            'Content-Type: application/json'
        ];

        $order_id           = "2237938826932135493";
        $orderObj           = OrderBaseData::where("order_id", $order_id)->first();
        $offer_id           = $orderObj->offer_id;
        $orderPrdObjs       = OrderProductData::where("order_id", $order_id)->get();
        $imgObj             = ProductImageData::where("offer_id", $offer_id)->where("img_type", "main")->where("lang", "kr")->first();
        $logicObjs          = OrderLogisticsData::where("order_id", $order_id)->groupBy("logistics_bill_no")->get();
        $logistics_bill_nos = $logicObjs->pluck('logistics_bill_no')->filter()->implode(',');
        $prdObj             = ProductData::where("offer_id", $offer_id)->first();
        
        $itemList = [];
        $optList  = [];
        
        foreach ($orderPrdObjs as $orderPrdObj) {
            $sku_id      = $orderPrdObj->sku_id;
            $optObj      = ProductOptionData::where("offer_id", $offer_id)->where("sku_id", $sku_id)->first();
            $sku_img_url = $optObj->sku_img_url;
            $productShno = "444";

            $itemList[]  = [
                "productShno"    => $productShno,
                "productNameEng" => $prdObj->prd_name_kr,
                "trackingNumber" => $logistics_bill_nos,
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
        $payload  = [
            "userId"    => BonaeraConstant::USER_ID,
            "orderMemo" => "요청사항 test",
            "itemList"  => $itemList
        ];

        $result = helpers_curl("POST", $endPoint, $header, $payload);
        // $result = [
        //     "code" => "1",
        //     "message" => "재고신청완료",
        //     "stockNo" => "ST240930000146",
        //     "item" => [
        //       0 => [
        //         "itCode" => "IT240930000147"
        //       ],
        //       1 => [
        //         "itCode" => "IT240930000148"
        //       ],
        //       2 => [
        //         "itCode" => "IT240930000149"
        //       ],
        //       3 => [
        //         "itCode" => "IT240930000150"
        //       ]
        //     ]
        // ];

        if( isset($result["stockNo"]) && isset($result["item"]) && !empty($result["item"]) ){
            $stockNo = $result["stockNo"];

            BonaeraInBaseData::updateOrCreate(
                [
                    "stock_no" => $stockNo,
                    "order_id" => $order_id,
                ],
                [
                    "offer_id" => $offer_id
                ]
            );

            foreach ($result["item"] as $key => $item) {
                $itCode = $item["itCode"];
                $opt    = $optList[$key];

                BonaeraInProductData::updateOrCreate(
                    [
                        "stock_no"  => $stockNo,
                        "order_id"  => $order_id,
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
        }

        dd(json_encode($payload, JSON_UNESCAPED_UNICODE), $result);
    }

    # php artisan test --filter testCreateShipping
    /** 출고신청 */
    public function testCreateShipping()
    {
        $endPoint = "https://bonaera.com/elpisapi/application_api.php";
        $header   = [
            'userKey: ' . env("BONAERA_TOKEN" , "3dI7uzN1dERvCBM1wt9wp1CglC7hcBB0jFkLZAFjZDC7SP56TIfwcJhfpTbLCIjg"),
            'Content-Type: application/json'
        ];

        $orderId = "2237938826932135493";
        
        $orderBaseObj     = OrderBaseData::where("order_id", $orderId)->first();
        $orderChannelObjs = OrderChannelData::where("order_id", $orderId)->get();
        $inBaseObj        = BonaeraInBaseData::where("order_id", $orderId)->first();
        $inPrdObjs        = BonaeraInProductData::where("order_id", $orderId)->get();
        
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

            $payload  = [
                "userId"  => BonaeraConstant::USER_ID,
                "ctrNum"  => 2,
                "RecInfo" => [
                    [
                        "receiverName"  => "홍길동",
                        "zipCode"       => "123456",
                        "addr1"         => "서울특별시 강남역",
                        "addr2"         => "201호",
                        "receiverPhone" => "01025466499",
                        "personalNum"   => "test01",
                        "shipMemo"      => "문 앞에 놓아주세요."
                    ]
                ],
                "itemList" => $itemList
            ];

            // $result = helpers_curl("POST", $endPoint, $header, $payload);
            $result = [
                "code"    => "1",
                "message" => "신청완료",
                "groupNo" => "GR240930000151",
                "orderNo" => "SH240930000152",
                "invoice" => "2222",
            ];

            if( isset($result["groupNo"]) && isset($result["orderNo"]) && isset($result["invoice"]) ){

                $outBaseObj = BonaeraOutBaseData::updateOrCreate(
                    [
                        "sh_no" => $result["orderNo"],
                    ],
                    [
                        "stock_no"         => $stockNo,
                        "order_id"         => $orderId,
                        "channel_order_id" => $channelOrderId,
                        "group_no"         => $result["groupNo"],
                        "out_ordered_at"   => null,
                        "out_completed_at" => null,
                    ]
                );

                foreach ($optList as $opt) {
                    BonaeraOutProductData::updateOrCreate(
                        [
                            "sh_no" => $result["orderNo"],
                        ],
                        [
                            "channel_order_id" => $channelOrderId,
                            "option_id"        => $opt["option_id"],
                            "it_code"          => $opt["it_code"],
                            "quantity"         => $opt["quantity"],
                            "shipped_qty"      => 0,
                        ]
                    );
                }

                $this->testCreateDelivery($outBaseObj);
            }
            
            dd(json_encode($payload, JSON_UNESCAPED_UNICODE), $result);
        }
    }

    # php artisan test --filter testCreateDelivery
    /** 신청서 조회 */
    public function testCreateDelivery(BonaeraOutBaseData $outBaseObj)
    {
        $endPoint = "https://bonaera.com/elpisapi/applicationList_api.php";
        $header   = [
            'userKey: ' . env("BONAERA_TOKEN" , "3dI7uzN1dERvCBM1wt9wp1CglC7hcBB0jFkLZAFjZDC7SP56TIfwcJhfpTbLCIjg"),
            'Content-Type: application/json'
        ];
        $payload  = [
            "userId"  => BonaeraConstant::USER_ID,
            "grCode"  => $outBaseObj->group_no,
        ];

        $result = helpers_curl("GET", $endPoint, $header, $payload);
        
        if( isset($result["data"]["grCode"]) && isset($result["data"]["ReciverInfo"][0]) ){
            $res         = $result["data"];
            $reciverInfo = $res["ReciverInfo"][0];

            BonaeraOutDeliveryData::updateOrCreate(
                [
                    "out_base_id" => $outBaseObj->id,
                ],
                [
                    "invoice"        => $res["invoice"] ?? "",
                    "state"          => $res["state"],
                    "outday"         => $res["outday"] ?? null,
                    "receiver_name"  => $reciverInfo["receiverName"],
                    "zip_code"       => $reciverInfo["zipCode"],
                    "addr1"          => $reciverInfo["addr1"],
                    "addr2"          => $reciverInfo["addr2"],
                    "receiver_phone" => $reciverInfo["receiverPhone"],
                    "personal_type"  => $reciverInfo["personalType"],
                    "personal_num"   => $reciverInfo["personalNum"],
                    "unipass_result" => $reciverInfo["unipassResult"],
                    "unipass_reason" => $reciverInfo["unipassReason"],
                    "ship_memo"      => $reciverInfo["shipMemo"],
                ]
            );

            if( isset($result["data"]["weightList"]) ){
                foreach ($result["data"]["weightList"] as $weight) {
                    BonaeraOutWeightData::updateOrCreate(
                        [
                            "out_base_id" => $outBaseObj->id,
                        ],
                        [
                            "box_cnt"          => $weight["boxCnt"] ?? 0,
                            "real_weight"      => $weight["realWeight"] ?? 0,
                            "width"            => $weight["width"] ?? 0,
                            "length"           => $weight["length"] ?? 0,
                            "height"           => $weight["height"] ?? 0,
                            "weight"           => $weight["weight"] ?? 0,
                            "ship_money"       => $weight["shipMoney"] ?? 0,
                            "weight_fee"       => $weight["weightFee"] ?? 0,
                            "volume_fee"       => $weight["volumeFee"] ?? 0,
                            "svc_money1"       => $weight["svcMoney1"] ?? 0,
                            "svc_money2"       => $weight["svcMoney2"] ?? 0,
                            "plus_money"       => $weight["plusMoney"] ?? 0,
                            "plus_money_memo"  => $weight["plusMoneyMemo"] ?? "",
                            "minus_money"      => $weight["minusMoney"] ?? 0,
                            "minus_money_memo" => $weight["minusMoneyMemo"] ?? "",
                            "commission"       => $weight["commission"] ?? 0,
                            "islands"          => $weight["islands"] ?? 0,
                            "total_money"      => $weight["totalMoney"] ?? 0,
                        ]
                    );
                }
            }
        }
    }

    # php artisan test --filter testGetStockList
    /** 재고현황 조회 */
    public function testGetStockList()
    {
        $endPoint = "https://bonaera.com/elpisapi/stockList_api.php";
        $header   = [
            'userKey: ' . env("BONAERA_TOKEN" , "3dI7uzN1dERvCBM1wt9wp1CglC7hcBB0jFkLZAFjZDC7SP56TIfwcJhfpTbLCIjg"),
            'Content-Type: application/json'
        ];
        $payload  = [
            "userId"  => BonaeraConstant::USER_ID,
            "stCode" => "ST240930000146",
        ];

        $result = helpers_curl("GET", $endPoint, $header, $payload);
        
       dd($result);
    }
}
