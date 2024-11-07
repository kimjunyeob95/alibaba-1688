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
use App\Models\ProductOptionData;
use Illuminate\Pagination\Paginator;
use App\Vo\Order\OrderChannelDetailDto;
use App\Vo\Order\OrderChannelDto;
use App\Vo\Order\OrderDto;
use Exception;
use Illuminate\Support\Facades\DB;
use Psr\Log\LogLevel;

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

    public function createWOrder(array $params, int $totalQuantity, bool $isPreview = false): array
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
            if( $detailResult["isSuccess"] != true || 
                !isset($detailResult["data"]["result"]["success"]) ||
                $detailResult["data"]["result"]["success"] != true ||
                !isset($detailResult["data"]["result"]["result"])
            ){
                throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
            }
            $detailProduct    = $detailResult["data"]["result"]["result"];
            $minOrderQuantity = 1;
            $batchNumber      = 1;

            if( isset($detailProduct["minOrderQuantity"]) ){
                $minOrderQuantity = $detailProduct["minOrderQuantity"];
            } else if( isset($detailProduct["productSaleInfo"]["priceRangeList"][0]["startQuantity"]) ){
                $minOrderQuantity = $detailProduct["productSaleInfo"]["priceRangeList"][0]["startQuantity"];
            } else {
                throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("MINORDERQUANTITY"));
            }

            if( isset($detailProduct["batchNumber"]) ){
                $batchNumber = $detailProduct["batchNumber"];
            }

            if( $prdObj->start_quantity != $minOrderQuantity ){
                ProductData::where("offer_id", $offerId)->update([
                    "start_quantity" => $minOrderQuantity
                ]);
                saveModiProduct($offerId);
            }

            if( $minOrderQuantity > $totalQuantity ){
                $errArray = [
                    "msg"            => OrderErrorMessageConstant::getFitErrorMessage("START_QUANTITY") . " 최소 구매 수량: {$minOrderQuantity} | 요청 수량: {$totalQuantity}",
                    "start_quantity" => $minOrderQuantity
                ];
                throw new ArrayValueError($errArray);
            }

            $cargoParamList = [];
            foreach ($params["optionParamList"] as $option) {
                $quantity = $option["quantity"];

                if ( ($quantity % $batchNumber) !== 0 ) {
                    /** batchNumber단위로 주문이 되어야함 */
                    $errArray = [
                        "msg"          => "option_id: " . $option["option_id"] . " | Error: 옵션 수량이 batch_number 단위로 지정해야 발주가 가능합니다.",
                        "batch_number" => $batchNumber
                    ];
                    throw new ArrayValueError($errArray);
                }

                $cargoParamList[] = [
                    "offerId"  => $offerId,
                    "specId"   => ($option["singleOption"] === true) ? "" : $option["specId"],
                    "quantity" => $quantity,
                ];
            }

            $endPoint = "param2/1/com.alibaba.trade/alibaba.createOrder.preview/";
            $payload = [
                'access_token' => $this->accessToken,
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
                'cargoParamList' => $cargoParamList,
            ];
            $previewResult = curl_1688("POST", $endPoint, $payload);
            
            if( !isset($previewResult["data"]["orderPreviewResuslt"]) ){
                throw new Exception(OrderErrorMessageConstant::getFitErrorMessage("ORDER_PREVIEW"));
            }

            if( $isPreview === true ){
                /** 주문 미리보기 시 바로 리턴 */
                $returnMsg = helpers_success_message($previewResult["data"]);
            } else {
                /** 주문 생성 */
                if( !isset($previewResult["data"]["orderPreviewResuslt"][0]["flowFlag"]) || empty($previewResult["data"]["orderPreviewResuslt"][0]["flowFlag"]) ){
                    throw new Exception(OrderErrorMessageConstant::getFitErrorMessage("FLOW"));
                }
                
                $flow = $previewResult["data"]["orderPreviewResuslt"][0]["flowFlag"];
    
                $endPoint = "param2/1/com.alibaba.trade/alibaba.trade.createCrossOrder/";
                $payload = [
                    'access_token' => $this->accessToken,
                    'flow'         => $flow,
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
                    'preSelectPayChannel' => Constant1688::PRESELECTPAYCHANNEL,
                    'useRedEnvelope'      => Constant1688::USEREDENVELOPE_N
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
            $pageSize     = (int)$params["pageSize"];
            $orderChannel = $params["orderChannel"];
            $orderStatus  = $params["orderStatus"];
            $orderStatus  = array_filter($orderStatus, function($value) {
                return !empty($value);
            });
            $deliveryStatus = $params["deliveryStatus"];
            $deliveryStatus = array_filter($deliveryStatus, function($value) {
                return !empty($value);
            });
            $refundStatus = $params["refundStatus"];
            $refundStatus = array_filter($refundStatus, function($value) {
                return !empty($value);
            });
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
                "w_options.option"
            ])
            ->join("order_channel_datas as b", "order_base_datas.order_id", "=", "b.order_id")
            ->leftJoin("order_trade_datas as c", "order_base_datas.order_id", "=", "c.order_id");

            if( !empty($orderChannel) ){
                $builder->where("order_base_datas.channel", $orderChannel);
            }
            if( !empty($orderStatus) ){
                $builder->whereIn("order_base_datas.status", $orderStatus);
            }
            if( !empty($deliveryStatus) ){
                $builder->whereHas('w_options', function($query) use ($deliveryStatus) {
                    $query->whereIn('logistics_status', $deliveryStatus);
                });
            }
            if( !empty($refundStatus) ){
                $builder->whereIn("order_base_datas.refund_status", $refundStatus);
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
            $orderChannel = $params["orderChannel"];
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

                foreach ($apiData["result"] as $key => &$data) {
                    $baseInfo = $data["baseInfo"];
                    $orderId  = $baseInfo["idOfStr"];

                    $data["baseObj"]    = OrderBaseData::where("order_id", $orderId)->first();
                    $data["channelObj"] = OrderChannelData::where("order_id", $orderId)->first();

                    if( !empty($orderChannel) ){
                        if( $data["baseObj"] == null || ($data["baseObj"]->channel != $orderChannel) ){
                            unset($apiData["result"][$key]);
                        }
                    }
                }

                $paginator = new LengthAwarePaginator(
                    collect($apiData["result"]), // 현재 페이지의 아이템들
                    $totalRecords, // 총 아이템 수
                    $pageSize, // 페이지 당 아이템 수
                    $page, // 현재 페이지
                    ['path' => LengthAwarePaginator::resolveCurrentPath()] // 현재 URL 경로
                );
            }

            $paginator->appends($params);

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
        $returnMsg   = $this->returnMsg;
        $successList = [];
        $failList    = [];
        foreach ($orderIds as $orderId) {
            try {
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
                    if( $orderBaseObj == null ){
                        throw new Exception(OrderErrorMessageConstant::getFitErrorMessage("BASE_INFO"));
                    }

                    $orderData = $orderDetailResult["data"]["result"];
                    $orderData["offerId"] = $orderBaseObj->offer_id;
                    $orderData["channel"] = $orderBaseObj->channel;
                    $orderDto = new OrderDto();
                    $orderDto->bind($orderData);
    
                    $updateResult = $this->upsertOrderBaseData($orderDto);
                    if( $updateResult["isSuccess"] == false ){
                        throw new Exception($updateResult["msg"]);
                    }
    
                    DB::commit();
                    $successList[] = $orderId;
                } catch (Exception $ee) {
                    DB::rollBack();
                    throw new Exception($ee->getMessage());
                }
            } catch (Exception $e) {
                $failList[] = [
                    "order_id" => $orderId,
                    "msg"    => $e->getMessage(),
                ];
            }
        }

        $returnMsg = helpers_success_message(["successList" => $successList, "failList" => $failList]);
        return $returnMsg;
    }

    /**
    * @func orderBatchUpdate
    * @description 'WApp 주문 배치 업데이트'
    * @return void
    */
    public function orderBatchUpdate(): void
    {
        $builder = OrderBaseData::select(["order_id"])->whereIn("status", OrderConstant::STATUS_BATCH_FILTER);

        $msg = "주문 업데이트 배치 시작";
        debug_log($msg, "order/batchUpdate", "batchUpdate");

        $perPage    = 900;
        $totalCount = $builder->count();
        $totalPages = ceil($totalCount / $perPage);

        for ($page = 1; $page <= $totalPages; $page++) {

            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });

            // paginate 메소드는 새 Paginator 인스턴스를 반환합니다.
            $pagedData = $builder->paginate($perPage);
            $results   = $pagedData->items();

            foreach ($results as $obj) {
                DB::beginTransaction();

                try {
                    $orderId           = $obj->order_id;
                    $orderDetailResult = $this->getWOrder($orderId);
                    if( $orderDetailResult["isSuccess"] == false || 
                        !isset($orderDetailResult["data"]["result"]["baseInfo"]) ||
                        empty($orderDetailResult["data"]["result"]["baseInfo"])
                    ){
                        throw new Exception(OrderErrorMessageConstant::getFitErrorMessage("W_DETAIL"));
                    }

                    $orderBaseObj = OrderBaseData::where("order_id", $orderId)->first();
                    if( $orderBaseObj == null){
                        throw new Exception(OrderErrorMessageConstant::getFitErrorMessage("BASE_INFO"));
                    }

                    $orderData = $orderDetailResult["data"]["result"];
                    $orderData["offerId"] = $orderBaseObj->offer_id;
                    $orderData["channel"] = $orderBaseObj->channel;
                    $orderDto = new OrderDto();
                    $orderDto->bind($orderData);

                    $updateResult = $this->upsertOrderBaseData($orderDto);
                    if( $updateResult["isSuccess"] == false ){
                        throw new Exception($updateResult["msg"]);
                    }

                    DB::commit();
                } catch (Exception $ee) {
                    DB::rollBack();

                    $msg = "주문 업데이트 배치 에러 | orderId: {$orderId} | 에러: " . $ee->getMessage();
                    debug_log($msg, "order/batchUpdate", "batchUpdate", LogLevel::ERROR);
                }
            }

            $msg = "주문 업데이트 배치 ({$page}/{$totalPages}) 완료";
            debug_log($msg, "order/batchUpdate", "batchUpdate");
        }

        $msg = "주문 업데이트 배치 종료";
        debug_log($msg, "order/batchUpdate", "batchUpdate");
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
    * @description '주문정보 전체 업데이트'
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
            $clearanceType        = trim($params["clearanceType"]);
            $shippingType         = trim($params["shippingType"]);
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

                $updateResult = $this->upsertOrderBaseData($orderDto);
                if( $updateResult["isSuccess"] == false ){
                    throw new Exception($updateResult["msg"]);
                }

                $orderChannelDtoBind = [
                    "orderId"              => $orderId,
                    "channelOrderId"       => $channelOrderId,
                    "clearanceType"        => $clearanceType,
                    "shippingType"         => $shippingType,
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

                $updateResult = $this->upsertOrderChannelData($orderChannelDto, $orderChannelDetailDtos);
                if( $updateResult["isSuccess"] == false ){
                    throw new Exception($updateResult["msg"]);
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
            
            $result = [];
            if( isset($curlResult["data"]["logisticsTrace"]) ){
                $result = $curlResult["data"]["logisticsTrace"];
            }
            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
    * @func orderCancel
    * @description 'W 주문 취소'
    * @param string $orderId
    * @return array
    */
    public function orderCancel(string $orderId): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $orderBaseObj = OrderBaseData::where("order_id", $orderId)->first();

            if( $orderBaseObj == null ){
                throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER"));
            }

            if( $orderBaseObj->status != OrderConstant::STATUS_WAITBUYERPAY ){
                throw new Exception(OrderErrorMessageConstant::getFitErrorMessage("STATUS_WAITBUYERPAY"));
            }

            $endPoint = "param2/1/com.alibaba.trade/alibaba.trade.cancel/";
            $payload = [
                'access_token' => $this->accessToken,
                'webSite'      => Constant1688::WEBSITE,
                'tradeID'      => (int)$orderId,
                'cancelReason' => OrderConstant::CANCEL_REASON_BUYER_OTHER,
            ];
            $curlResult = curl_1688("POST", $endPoint, $payload);

            try {
                DB::beginTransaction();

                $orderDetailResult = $this->getWOrder($orderId);
                if( $orderDetailResult["isSuccess"] == false || 
                    !isset($orderDetailResult["data"]["result"]["baseInfo"]) ||
                    empty($orderDetailResult["data"]["result"]["baseInfo"])
                ){
                    throw new Exception(OrderErrorMessageConstant::getFitErrorMessage("W_DETAIL"));
                }

                $orderData = $orderDetailResult["data"]["result"];
                $orderData["offerId"] = $orderBaseObj->offer_id;
                $orderData["channel"] = $orderBaseObj->channel;
                $orderDto = new OrderDto();
                $orderDto->bind($orderData);

                $updateResult = $this->upsertOrderBaseData($orderDto);
                if( $updateResult["isSuccess"] == false ){
                    throw new Exception($updateResult["msg"]);
                }

                DB::commit();
            } catch (Exception $ee) {
                DB::rollBack();
                throw new Exception($ee->getMessage());
            }

            if( isset($curlResult["data"]["success"]) && $curlResult["data"]["success"] == true ){
                $returnMsg = helpers_success_message([], "주문이 취소 되었습니다.");
            } else {
                $msg = OrderErrorMessageConstant::getFitErrorMessage("CANCEL_API");
                if( isset($curlResult["data"]["errorMessage"]) && $curlResult["data"]["errorMessage"] ){
                    $msg = $msg . " | w errorMessage: " . $curlResult["data"]["errorMessage"];
                }
                throw new Exception($msg);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function queueTest(): void
    {
        $params = [
            "version" => 3
        ];
        debug_log(json_encode($params, JSON_UNESCAPED_UNICODE), "1688/queueTest", "queueTest");
    }
}