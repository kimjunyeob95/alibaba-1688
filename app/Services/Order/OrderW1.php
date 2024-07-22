<?php

namespace App\Services\Order;

use App\Abstracts\OrderAbstract;
use App\Constants\Constant1688;
use App\Constants\OrderConstant;
use App\Constants\OrderErrorMessageConstant;
use App\Constants\ProductErrorMessageConstant;
use App\Exceptions\ArrayValueError;
use App\Models\ProductData;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\OrderBaseData;
use App\Models\OrderChannelData;
use App\Models\OrderChannelDetailData;
use App\Models\OrderLogisticsData;
use App\Models\OrderProductData;
use App\Models\OrderTradeData;
use App\Models\ProductOptionData;
use App\Vo\Order\OrderChannelDetailDto;
use App\Vo\Order\OrderChannelDto;
use App\Vo\Order\OrderDto;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderW1 extends OrderAbstract
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getWOrder(string $orderId): array
    {
        $returnMsg = $this->returnMsg;

        try {

            $endPoint = "param2/1/com.alibaba.trade/alibaba.trade.get.buyerView/";
            $payload = [
                'access_token' => $this->accessToken,
                'webSite'      => Constant1688::WEBSITE,
                'orderId'      => (int)$orderId,
            ];
            $result = curl_1688("post", $endPoint, $payload);

            if( $result["isSuccess"] === true &&
                isset($result["data"]) &&
                isset($result["data"]["result"])
            ){
                $returnMsg = helpers_success_message($result["data"]);
            } else {
                $errorMsg = $this->returnMsg["msg"];
                if( isset($result["data"]["errorMessage"]) ){
                    $errorMsg = $result["data"]["errorMessage"];
                } else if( isset($result["data"]["error_message"]) ){
                    $errorMsg = $result["data"]["error_message"];
                }

                $returnMsg = helpers_fail_message($errorMsg);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function createWOrder(array $params, int $totalQuantity): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $offerId = $params["offerId"];
            $prdObj  = ProductData::where("offer_id", $offerId)->first();
            if( $prdObj == null ){
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT"));   
            }

            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
            $payload  = [
                'access_token'     => $this->accessToken,
                'offerDetailParam' => [
                    'offerId' => $offerId,
                    'country' => Constant1688::LANGUAGE_KO,
                ]
            ];
            $detailResult = curl_1688("POST", $endPoint, $payload);
            if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
            }
            $detailProduct = $detailResult["data"]["result"]["result"];
            $startQuantity = $detailProduct["productSaleInfo"]["priceRangeList"][0]["startQuantity"];

            if( $prdObj->start_quantity != $startQuantity ){
                ProductData::where("offer_id", $offerId)->update([
                    "start_quantity" => $startQuantity
                ]);
            }

            if( $startQuantity > $totalQuantity ){
                $errArray = [
                    "msg"            => OrderErrorMessageConstant::getFitErrorMessage("START_QUANTITY") . " 최소 구매 수량: {$startQuantity} | 요청 수량: {$totalQuantity}",
                    "start_quantity" => $startQuantity
                ];
                throw new ArrayValueError($errArray);
            }

            $cargoParamList = [];
            foreach ($params["optionParamList"] as $option) {
                $cargoParamList[] = [
                    "offerId"  => $offerId,
                    "specId"   => $option["specId"],
                    "quantity" => $option["quantity"],
                ];
            }

            $endPoint = "param2/1/com.alibaba.trade/alibaba.trade.createCrossOrder/";
            $payload = [
                'access_token' => $this->accessToken,
                'flow'         => Constant1688::FLOW,
                'addressParam' => [
                    'addressId'    => Constant1688::ADDRESSID,
                    'fullName'     => Constant1688::FULLNAME,
                    'mobile'       => Constant1688::MOBILE,
                    'phone'        => Constant1688::PHONE,
                    'postCode'     => Constant1688::POSTCODE,
                    'cityText'     => Constant1688::CITYTEXT,
                    'provinceText' => Constant1688::PROVINCETEXT,
                    'areaText'     => Constant1688::AREATEXT,
                    'townText'     => Constant1688::TOWNTEXT,
                    'address'      => Constant1688::ADDRESS,
                    'districtCode' => Constant1688::DISTRICTCODE,
                ],
                'cargoParamList'      => $cargoParamList,
                'preSelectPayChannel' => Constant1688::PRESELECTPAYCHANNEL
            ];
            $result = curl_1688("post", $endPoint, $payload);

            if( $result["isSuccess"] === true &&
                isset($result["data"]) &&
                $result["data"]["success"] === true &&
                isset($result["data"]["result"]["orderId"])
            ){
                $returnMsg = helpers_success_message($result["data"]["result"]);
            } else {
                $errorMsg = $this->returnMsg["msg"];
                if( isset($result["data"]["message"]) ){
                    $errorMsg = $result["data"]["message"];
                } else if( isset($result["data"]["code"]) ){
                    $errorMsg = $result["data"]["code"];
                }

                $returnMsg = helpers_fail_message($errorMsg);

                debug_log(json_encode($result, JSON_UNESCAPED_UNICODE), "createWOrder", "createWOrder");
            }
        }  catch (ArrayValueError $e) {
            $errorArray = $e->getErrorArray();

            $returnMsg = helpers_fail_message($errorArray["msg"], $errorArray);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
    * @func orderList
    * @description 'WApp 주문 리스트'
    * @param array $params
    * @return array
    */
    public function orderList(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $pageSize       = (int)$params["pageSize"];
            $orderChannel   = $params["orderChannel"];
            $orderStatus    = $params["orderStatus"];
            $deliveryStatus = $params["deliveryStatus"];
            $refundStatus   = $params["refundStatus"];
            $timeCls        = $params["timeCls"];
            $startTime      = $params["startTime"];
            $endTime        = $params["endTime"];
            $search_cls     = $params["search_cls"];
            $keyword        = $params["keyword"];
            $sortArr        = explode("|", $params["sort"]);

            $builder = OrderBaseData::select([
                "order_base_datas.*",
                "b.channel_order_id",
                "b.buyer_name",
                "b.total_channel_price",
                "c.phas_amount",
            ])
            ->with([
                "product.main_img",
                "w_options.option",
                "logistics"
            ])
            ->join("order_channel_datas as b", "order_base_datas.order_id", "=", "b.order_id")
            ->leftJoin("order_trade_datas as c", "order_base_datas.order_id", "=", "c.order_id");

            if( !empty($orderChannel) ){
                $builder->where("order_base_datas.channel", $orderChannel);
            }
            if( !empty($orderStatus) ){
                $builder->where("order_base_datas.status", $orderStatus);
            }
            if( !empty($deliveryStatus) ){
                $builder->whereHas('w_options', function($query) use ($deliveryStatus) {
                    $query->where('logistics_status', $deliveryStatus);
                });
            }
            if( !empty($refundStatus) ){
                $builder->where("order_base_datas.refund_status", $refundStatus);
            }
            if( !empty($timeCls) ){
                if( $timeCls == "createOrder" ){
                    if( !empty($startTime) ) {
                        $builder->where("order_base_datas.created_at", ">=", $startTime . " 00:00:00");
                    }
                    if( !empty($endTime) ) {
                        $builder->where("order_base_datas.created_at", "<=", $endTime . " 23:59:59");
                    }
                } else if( $timeCls == "modiOrder" ){
                    if( !empty($startTime) ) {
                        $builder->where("order_base_datas.updated_at", ">=", $startTime . " 00:00:00");
                    }
                    if( !empty($endTime) ) {
                        $builder->where("order_base_datas.updated_at", "<=", $endTime . " 23:59:59");
                    }
                }
            }
            if( !empty($keyword) ){
                $keyword = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
                $keyword = explode(",", $keyword);
                // 각 배열 요소의 앞뒤 공백 제거
                $keyword = array_map('trim', $keyword);
                // 빈 값을 제거
                $keyword = array_filter($keyword);
                // 중복 제거
                $keyword = array_unique($keyword);

                if( $search_cls == "order_id"){
                    $builder->whereIn("order_base_datas." . $search_cls, $keyword);
                } else if( $search_cls == "channel_order_id"){
                    $builder->whereIn("b." . $search_cls, $keyword);
                } else if( $search_cls == "offer_id" ){
                    $builder->whereIn("order_base_datas." . $search_cls, $keyword);
                }
            }
            $builder->orderBy("order_base_datas." . $sortArr[0], $sortArr[1]);

            $lists = $builder->paginate($pageSize)->appends($params);

            $returnMsg = helpers_success_message($lists);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
    * @func orderWList
    * @description 'W 주문 리스트'
    * @param array $params
    * @return array
    */
    public function orderWList(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $page         = (int)$params["page"];
            $pageSize     = (int)$params["pageSize"];
            $orderStatus  = $params["orderStatus"];
            $refundStatus = $params["refundStatus"];
            $timeCls      = $params["timeCls"];
            $startTime    = $params["startTime"];
            $endTime      = $params["endTime"];

            $endPoint = "param2/1/com.alibaba.trade/alibaba.trade.getBuyerOrderList/";
            $payload = [
                'access_token' => $this->accessToken,
                'page'         => $page,
                'pageSize'     => $pageSize,
            ];

            if( !empty($orderStatus) ){
                $payload["orderStatus"] = $orderStatus;
            }
            if( !empty($refundStatus) ){
                $payload["refundStatus"] = $refundStatus;
            }
            if( !empty($timeCls) ){
                if( $timeCls == "createOrder" ){
                    if( !empty($startTime) ) {
                        $payload["createStartTime"] = formatToCST($startTime);
                    }
                    if( !empty($endTime) ) {
                        $payload["createEndTime"] = formatToCST($endTime);
                    }
                } else if( $timeCls == "modiOrder" ){
                    if( !empty($startTime) ) {
                        $payload["modifyStartTime"] = formatToCST($startTime);
                    }
                    if( !empty($endTime) ) {
                        $payload["modifyEndTime"] = formatToCST($endTime);
                    }
                }
            }
            $curlResult = curl_1688("get", $endPoint, $payload);
            $paginator  = new LengthAwarePaginator(collect(), 0, $page, $pageSize);
            if( isset($curlResult["data"]["result"]) && !empty($curlResult["data"]["result"]) ){
                $apiData      = $curlResult["data"];
                $totalRecords = $apiData["totalRecord"];

                foreach ($apiData["result"] as &$data) {
                    $baseInfo = $data["baseInfo"];
                    $orderId  = $baseInfo["idOfStr"];
                    
                    $data["baseObj"]    = OrderBaseData::where("order_id", $orderId)->first();
                    $data["channelObj"] = OrderChannelData::where("order_id", $orderId)->first();
                }

                $paginator = new LengthAwarePaginator(
                    collect($apiData["result"]), // 현재 페이지의 아이템들
                    $totalRecords, // 총 아이템 수
                    $pageSize, // 페이지 당 아이템 수
                    $page, // 현재 페이지
                    ['path' => LengthAwarePaginator::resolveCurrentPath()] // 현재 URL 경로
                );
            }

            $returnMsg = helpers_success_message($paginator);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
    * @func orderUpdate
    * @description 'W -> WApp 주문 업데이트'
    * @param array $orderIds
    * @return array
    */
    public function orderUpdate(array $orderIds): array
    {
        $returnMsg = $this->returnMsg;
        try {
            foreach ($orderIds as $orderId) {
                $orderDetailResult = $this->getWOrder($orderId);
                if( $orderDetailResult["isSuccess"] == false || 
                    !isset($orderDetailResult["data"]["result"]["baseInfo"]) ||
                    empty($orderDetailResult["data"]["result"]["baseInfo"])
                ){
                    throw new Exception(OrderErrorMessageConstant::getFitErrorMessage("W_DETAIL"));
                }

                try {
                    DB::beginTransaction();

                    $orderBaseObj = OrderBaseData::where("order_id", $orderId)->first();
                    if( $orderBaseObj == null){
                        throw new Exception(OrderErrorMessageConstant::getFitErrorMessage("BASE_INFO"));
                    }

                    $orderData = $orderDetailResult["data"]["result"];
                    $orderData["offerId"] = $orderBaseObj->offer_id;
                    $orderData["channel"] = $orderBaseObj->channel;
                    $orderDto = new OrderDto();
                    $orderDto->bind($orderData);
                    $baseInfo    = $orderDto->orderBaseDto;
                    $upsertWhere = $baseInfo->getAllProperties();
                    unset($upsertWhere["order_id"]);
                    OrderBaseData::updateOrCreate(
                        [
                            "order_id" => $baseInfo->order_id,
                        ],
                        $upsertWhere
                    );
    
                    $orderTradeDtos = $orderDto->orderTradeDtos;
                    foreach ($orderTradeDtos as $orderTradeDto) {
                        $upsertWhere = $orderTradeDto->getAllProperties();
                        unset($upsertWhere["order_id"]);
                        unset($upsertWhere["phase"]);
                        OrderTradeData::updateOrCreate(
                            [
                                "order_id" => $orderTradeDto->order_id,
                                "phase"    => $orderTradeDto->phase,
                            ],
                            $upsertWhere
                        );
                    }
    
                    $orderProductDtos = $orderDto->orderProductDtos;
                    foreach ($orderProductDtos as $orderProductDto) {
                        $upsertWhere = $orderProductDto->getAllProperties();
                        unset($upsertWhere["order_id"]);
                        unset($upsertWhere["offer_id"]);
                        unset($upsertWhere["spec_id"]);
                        OrderProductData::updateOrCreate(
                            [
                                "order_id" => $orderProductDto->order_id,
                                "offer_id" => $orderProductDto->offer_id,
                                "spec_id"  => $orderProductDto->spec_id,
                            ],
                            $upsertWhere
                        );
                    }
    
                    $orderLogisticDtos = $orderDto->orderLogisticDtos;
                    foreach ($orderLogisticDtos as $orderLogisticDto) {
                        $upsertWhere = $orderLogisticDto->getAllProperties();
                        unset($upsertWhere["logistics_id"]);
                        OrderLogisticsData::updateOrCreate(
                            [
                                "logistics_id" => $orderLogisticDto->logistics_id,
                            ],
                            $upsertWhere
                        );
                    }
    
                    DB::commit();
                } catch (Exception $ee) {
                    DB::rollBack();
                }
            }

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
  
    /**
    * @func orderPayLinkCreate
    * @description 'WApp 주문 결제 링크 생성'
    * @param array $params
    * @return array
    */
    public function orderPayLinkCreate(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $orderIds = $params["orderIds"];
            $payWay   = $params["payWay"];

            if( $payWay == OrderConstant::PAY_ALIPAY ){
                $endPoint = "param2/1/com.alibaba.trade/alibaba.alipay.url.get/";
            } else if( $payWay == OrderConstant::PAY_CROSS_BORDER ){
                $endPoint = "param2/1/com.alibaba.trade/alibaba.crossBorderPay.url.get/";
            }

            $payload = [
                'access_token' => $this->accessToken,
                'orderIdList'  => $orderIds,
            ];

            $curlResult = curl_1688("post", $endPoint, $payload);
            if( isset($curlResult["data"]["payUrl"]) ){
                $returnMsg = helpers_success_message($curlResult["data"]);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
    * @func orderInfoUpdate
    * @description '주문정보 업데이트'
    * @param array $params
    * @return array
    */
    public function orderInfoUpdate(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $orderId              = trim($params["orderId"]);
            $channelPrices        = $params["channelPrices"];
            $orderChannel         = trim($params["orderChannel"]);
            $channelOrderId       = trim($params["channelOrderId"]);
            $deliveryPrice        = (float)$params["deliveryPrice"];
            $buyerName            = trim($params["buyerName"]);
            $buyerClearanceNumber = trim($params["buyerClearanceNumber"]);
            $buyerNumber          = trim($params["buyerNumber"]);
            $buyerPhone           = trim($params["buyerPhone"]);
            $buyerAddress         = trim($params["buyerAddress"]);
            $buyerZipcode         = trim($params["buyerZipcode"]);
            $buyerMemo            = trim($params["buyerMemo"]);

            $orderDetailResult = $this->getWOrder($orderId);
            if( $orderDetailResult["isSuccess"] == false || 
                !isset($orderDetailResult["data"]["result"]["baseInfo"]) ||
                empty($orderDetailResult["data"]["result"]["baseInfo"])
            ){
                throw new Exception(OrderErrorMessageConstant::getFitErrorMessage("W_DETAIL"));
            }

            try {
                DB::beginTransaction();

                $orderData = $orderDetailResult["data"]["result"];
                $offerId   = $orderData["productItems"][0]["productID"];

                $prdCnt = ProductData::where("offer_id", $offerId)->count();
                if( $prdCnt == 0 ){
                    throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("PRODUCT"));
                }

                $orderData["offerId"] = $offerId;
                $orderData["channel"] = $orderChannel;
                $orderDto             = new OrderDto();
                $orderDto->bind($orderData);

                $totalQuantity     = 0;
                $totalPrice        = 0;
                $totalChannelPrice = 0;

                foreach ($orderDto->orderProductDtos as $key => $orderProductDto) {
                    $channelPrice = $channelPrices[$key] ?? 0;
                    $quantity     = $orderProductDto->quantity;

                    $optObj = ProductOptionData::where([
                        "offer_id" => $offerId,
                        "spec_id"  => $orderProductDto->spec_id,
                        "sku_id"   => $orderProductDto->sku_id,
                    ])->first();

                    if( $optObj == null ){
                        throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("OPTION"));
                    }

                    $totalQuantity     += $quantity;
                    $totalPrice         = $totalPrice + ( $optObj->price_1688_option * $quantity );
                    $totalChannelPrice  = $totalChannelPrice + ( $channelPrice * $quantity );

                    $orderChannelDetailDtoBind = [
                        "orderChannelId" => 0,
                        "optionId"       => $optObj->id,
                        "originPrice"    => $optObj->price_1688_option,
                        "channelPrice"   => $channelPrice,
                        "quantity"       => $quantity,
                    ];
                    $orderChannelDetailDto = new OrderChannelDetailDto();
                    $orderChannelDetailDto->bind($orderChannelDetailDtoBind);
    
                    $orderChannelDetailDtos[] = $orderChannelDetailDto;
                }

                $baseInfo    = $orderDto->orderBaseDto;
                $upsertWhere = $baseInfo->getAllProperties();
                unset($upsertWhere["order_id"]);
                OrderBaseData::updateOrCreate(
                    [
                        "order_id" => $baseInfo->order_id,
                    ],
                    $upsertWhere
                );

                $orderTradeDtos = $orderDto->orderTradeDtos;
                foreach ($orderTradeDtos as $orderTradeDto) {
                    $upsertWhere = $orderTradeDto->getAllProperties();
                    unset($upsertWhere["order_id"]);
                    unset($upsertWhere["phase"]);
                    OrderTradeData::updateOrCreate(
                        [
                            "order_id" => $orderTradeDto->order_id,
                            "phase"    => $orderTradeDto->phase,
                        ],
                        $upsertWhere
                    );
                }

                $orderProductDtos = $orderDto->orderProductDtos;
                foreach ($orderProductDtos as $orderProductDto) {
                    $upsertWhere = $orderProductDto->getAllProperties();
                    unset($upsertWhere["order_id"]);
                    unset($upsertWhere["offer_id"]);
                    unset($upsertWhere["spec_id"]);
                    OrderProductData::updateOrCreate(
                        [
                            "order_id" => $orderProductDto->order_id,
                            "offer_id" => $orderProductDto->offer_id,
                            "spec_id"  => $orderProductDto->spec_id,
                        ],
                        $upsertWhere
                    );
                }

                $orderLogisticDtos = $orderDto->orderLogisticDtos;
                foreach ($orderLogisticDtos as $orderLogisticDto) {
                    $upsertWhere = $orderLogisticDto->getAllProperties();
                    unset($upsertWhere["logistics_id"]);
                    OrderLogisticsData::updateOrCreate(
                        [
                            "logistics_id" => $orderLogisticDto->logistics_id,
                        ],
                        $upsertWhere
                    );
                }

                $orderChannelDtoBind = [
                    "orderId"              => $orderId,
                    "channelOrderId"       => $channelOrderId,
                    "totalQuantity"        => $totalQuantity,
                    "totalPrice"           => $totalPrice,
                    "deliveryPrice"        => $deliveryPrice,
                    "totalChannelPrice"    => $totalChannelPrice,
                    "buyerName"            => $buyerName,
                    "buyerClearanceNumber" => $buyerClearanceNumber,
                    "buyerNumber"          => $buyerNumber,
                    "buyerPhone"           => $buyerPhone,
                    "buyerZipcode"         => $buyerZipcode,
                    "buyerAddress"         => $buyerAddress,
                    "buyerMemo"            => $buyerMemo,
                ];
                $orderChannelDto = new OrderChannelDto();
                $orderChannelDto->bind($orderChannelDtoBind);

                $upsertWhere = $orderChannelDto->getAllProperties();
                unset($upsertWhere["order_id"]);
                $ocdObj = OrderChannelData::updateOrCreate(
                    [
                        "order_id" => $orderChannelDto->order_id,
                    ],
                    $upsertWhere
                );

                foreach ($orderChannelDetailDtos as $orderChannelDetailDto) {
                    $upsertWhere = $orderChannelDetailDto->getAllProperties();
                    unset($upsertWhere["order_channel_id"]);
                    unset($upsertWhere["option_id"]);
                    OrderChannelDetailData::updateOrCreate(
                        [
                            "order_channel_id" => $ocdObj->id,
                            "option_id"        => $orderChannelDetailDto->option_id
                        ],
                        $upsertWhere
                    );
                }

                DB::commit();

                $returnMsg = helpers_success_message();
            } catch (Exception $ee) {
                DB::rollBack();

                $returnMsg = helpers_fail_message($ee->getMessage());
            }

        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
    * @func orderLogisticsInfo
    * @description 'W 주문 물류 조회'
    * @param string $orderId
    * @return array
    */
    public function orderLogisticsInfo(string $orderId): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $endPoint = "param2/1/com.alibaba.logistics/alibaba.trade.getLogisticsTraceInfo.buyerView/";
            $payload = [
                'access_token' => $this->accessToken,
                'orderId'      => $orderId,
                'webSite'      => Constant1688::WEBSITE,
            ];

            $curlResult = curl_1688("get", $endPoint, $payload);
            dd($curlResult);
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}