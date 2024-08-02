<?php

namespace App\Abstracts;

use App\Constants\OrderErrorMessageConstant;
use App\Models\OrderBaseData;
use App\Models\OrderChannelData;
use App\Models\OrderChannelDetailData;
use App\Models\OrderLogisticsData;
use App\Models\OrderProductData;
use App\Models\OrderTradeData;
use App\Vo\Order\OrderChannelDetailDto;
use App\Vo\Order\OrderChannelDto;
use App\Vo\Order\OrderDto;
use Exception;
use Illuminate\Support\Facades\DB;

abstract class OrderAbstract
{
    protected array $returnMsg;
    protected string $accessToken;

    public function __construct()
    {
        $this->returnMsg   = helpers_fail_message();
        $this->accessToken = env("1688_ACCESS_TOKEN");
    }

    /**
    * @func getWOrder
    * @description 'W 주문 조회'
    * @param string $orderId
    * @return array
    */
    abstract function getWOrder(string $orderId): array;

    /**
    * @func createWOrder
    * @description 'W 주문 생성'
    * @param array $params
    * @param int $totalQuantity
    * @return array
    */
    abstract function createWOrder(array $params, int $totalQuantity): array;

    /**
    * @func orderList
    * @description 'WApp 주문 리스트'
    * @param array $params
    * @return array
    */
    abstract function orderList(array $params): array;

    /**
    * @func orderWList
    * @description 'W 주문 리스트'
    * @param array $params
    * @return array
    */
    abstract function orderWList(array $params): array;

    /**
    * @func orderUpdate
    * @description 'W -> WApp 주문 업데이트'
    * @param array $orderIds
    * @return array
    */
    abstract function orderUpdate(array $orderIds): array;

    /**
    * @func orderBatchUpdate
    * @description 'WApp 주문 배치 업데이트'
    * @return void
    */
    abstract function orderBatchUpdate(): void;

    /**
    * @func orderPayLinkCreate
    * @description 'WApp 주문 결제 링크 생성'
    * @param array $params
    * @return array
    */
    abstract function orderPayLinkCreate(array $orderIds): array;

    /**
    * @func orderInfoUpdate
    * @description '주문정보 전체 업데이트'
    * @param array $params
    * @return array
    */
    abstract function orderInfoUpdate(array $params): array;

    /**
    * @func orderWappInfo
    * @description 'WApp 주문 조회'
    * @param string $orderId
    * @return array
    */
    public function orderWappInfo(string $orderId): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $obj = OrderBaseData::with([
                "product.main_img",
                "logistics",
                "w_options.option",
                "channel_objs.details"
            ])->where("order_id", $orderId)->first();

            $returnMsg = helpers_success_message($obj);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
    
    /**
    * @func upsertOrderBaseData
    * @description '주문 기본 정보 upsert'
    * @param OrderDto $orderDto
    * @return array
    */
    public function upsertOrderBaseData(OrderDto $orderDto): array
    {
        $returnMsg = $this->returnMsg;

        try {
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

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message("method: upsertOrderBaseData | error: " . $e->getMessage());
        }

        return $returnMsg;
    }

    /**
    * @func upsertOrderChannelData
    * @description '주문 채널 정보 upsert'
    * @param OrderChannelDto $orderChannelDto
    * @param array $orderChannelDetailDtos
    * @return array
    */
    public function upsertOrderChannelData(OrderChannelDto $orderChannelDto, array $orderChannelDetailDtos): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $upsertWhere = $orderChannelDto->getAllProperties();
            unset($upsertWhere["order_id"]);
            unset($upsertWhere["channel_order_id"]);
            $ocdObj = OrderChannelData::updateOrCreate(
                [
                    "order_id"         => $orderChannelDto->order_id,
                    "channel_order_id" => $orderChannelDto->channel_order_id,
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

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message("method: upsertOrderChannelData | error: " . $e->getMessage());
        }

        return $returnMsg;
    }

    /**
    * @func orderLogisticsInfo
    * @description 'W 주문 물류 조회'
    * @param string $orderId
    * @return array
    */
    abstract function orderLogisticsInfo(string $orderId): array;

    /**
    * @func orderCancel
    * @description 'W 주문 취소'
    * @param string $orderId
    * @return array
    */
    abstract function orderCancel(string $orderId): array;

    /**
    * @func orderInfoChannelUpdate
    * @description 'WApp 주문 채널정보 업데이트'
    * @param array $params
    * @return array
    */
    public function orderInfoChannelUpdate(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $orderId        = $params["order_id"];
            $changeChannels = $params["change_channels"];
            $addChannels    = $params["add_channels"];
            $orderObj       = OrderBaseData::with([
                "w_options.option",
                "channel_objs.details"
            ])->where("order_id", $orderId)->first();

            if( $orderObj == null ){
                throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("BASE_INFO"));
            }

            $wOptionIds              = [];
            $wOptionQuantities       = [];
            $allChannelOptQuantities = [];
            $wOptionPrices           = [];
            $allChannelOrderIds      = [];
            foreach ($orderObj->w_options as $wOption) {
                $wappOption                               = $wOption->option;
                $wOptionIds[]                             = $wappOption->id;
                $wOptionQuantities[$wappOption->id]       = $wOption->quantity;
                $allChannelOptQuantities[$wappOption->id] = 0;
                $wOptionPrices[$wappOption->id]           = $wOption->price;
            }

            /** w주문에 속한 옵션id 체크 */
            foreach ($changeChannels as $changeChannel) {
                if( isset($changeChannel["options"]) && !empty($changeChannel["options"]) ){
                    foreach ($changeChannel["options"] as $opt) {
                        if( !in_array($opt["opt_id"], $wOptionIds) ){
                            throw new Exception("change_channels: " . OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_NOT_IN_OPTION_ID"));
                        }
                        $allChannelOptQuantities[$opt["opt_id"]] += (int)$opt["quantity"];
                    }
                }
                $allChannelOrderIds[] = $changeChannel["channel_order_id"];
            }
            /** w주문에 속한 옵션id 체크 */
            foreach ($addChannels as $addChannel) {
                if( isset($addChannel["options"]) && !empty($addChannel["options"]) ){
                    foreach ($addChannel["options"] as $opt) {
                        if( !in_array($opt["opt_id"], $wOptionIds) ){
                            throw new Exception("change_channels: " . OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_NOT_IN_OPTION_ID"));
                        }
                        $allChannelOptQuantities[$opt["opt_id"]] += (int)$opt["quantity"];
                    }
                }
                $allChannelOrderIds[] = $addChannel["channel_order_id"];
            }
            /** 중복 채널 주문번호 체크 */
            $duplicates = collect($allChannelOrderIds)->duplicates();
            if ($duplicates->isNotEmpty()) {
                $duplicateValues = $duplicates->unique()->values()->all();
                $duplicateValuesString = implode(', ', $duplicateValues);
                throw new Exception(OrderErrorMessageConstant::getFitErrorMessage("SAME_CHANNEL_ORDER_ID") . "\r\n중복 주문번호: " . $duplicateValuesString);
            }
            /** w주문과 수량 비교 */
            foreach ($wOptionQuantities as $optId => $wOptionQuantity) {
                $allChannelOptQuantity = $allChannelOptQuantities[$optId];
                if( $wOptionQuantity != $allChannelOptQuantity ){
                    throw new Exception(OrderErrorMessageConstant::getFitErrorMessage("ORDER_EQUAL_OPTION"));
                }
            }

            try {
                DB::beginTransaction();

                /** 1. 기존 주문 update */
                foreach ($changeChannels as $changeChannel) {
                    $orderChannelDetailDtos = [];

                    if( isset($changeChannel["options"]) && !empty($changeChannel["options"]) ){
                        $totalQuantity     = 0;
                        $totalPrice        = 0;
                        $totalChannelPrice = 0;
                        foreach ($changeChannel["options"] as $option) {
                            $optionQuantity     = (int)$option["quantity"];
                            $totalQuantity     += $optionQuantity;
                            $totalPrice        += $optionQuantity * $wOptionPrices[$option["opt_id"]];
                            $totalChannelPrice += $optionQuantity * (int)$option["channel_price"];

                            $orderChannelDetailDtoBind = [
                                "orderChannelId" => 0,
                                "optionId"       => $option["opt_id"],
                                "originPrice"    => $wOptionPrices[$option["opt_id"]],
                                "channelPrice"   => (int)$option["channel_price"],
                                "quantity"       => $optionQuantity,
                            ];
                            $orderChannelDetailDto = new OrderChannelDetailDto();
                            $orderChannelDetailDto->bind($orderChannelDetailDtoBind);
            
                            $orderChannelDetailDtos[] = $orderChannelDetailDto;
                        }
                        $orderChannelDtoBind = [
                            "orderId"              => $orderId,
                            "channelOrderId"       => $changeChannel["channel_order_id"],
                            "totalQuantity"        => $totalQuantity,
                            "totalPrice"           => $totalPrice,
                            "deliveryPrice"        => $changeChannel["delivery_price"],
                            "totalChannelPrice"    => $totalChannelPrice,
                            "buyerName"            => $changeChannel["buyer_name"],
                            "buyerClearanceNumber" => $changeChannel["buyer_clearance_number"],
                            "buyerNumber"          => $changeChannel["buyer_number"],
                            "buyerPhone"           => $changeChannel["buyer_phone"],
                            "buyerZipcode"         => $changeChannel["buyer_zipcode"],
                            "buyerAddress"         => $changeChannel["buyer_address"],
                            "buyerMemo"            => $changeChannel["buyer_memo"],
                        ];
                        $orderChannelDto = new OrderChannelDto();
                        $orderChannelDto->bind($orderChannelDtoBind);
                    } else {
                        $orderChannelObj = OrderChannelData::where([
                            "order_id"         => $orderId,
                            "channel_order_id" => $changeChannel["channel_order_id"],
                        ])->first();

                        if( $orderChannelObj == null ){
                            throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER_CHANNEL"));
                        }

                        $orderChannelDtoBind = [
                            "orderId"              => $orderId,
                            "channelOrderId"       => $changeChannel["channel_order_id"],
                            "totalQuantity"        => $orderChannelObj->total_quantity,
                            "totalPrice"           => $orderChannelObj->total_price,
                            "deliveryPrice"        => $orderChannelObj->delivery_price,
                            "totalChannelPrice"    => $orderChannelObj->total_channel_price,
                            "buyerName"            => $changeChannel["buyer_name"],
                            "buyerClearanceNumber" => $changeChannel["buyer_clearance_number"],
                            "buyerNumber"          => $changeChannel["buyer_number"],
                            "buyerPhone"           => $changeChannel["buyer_phone"],
                            "buyerZipcode"         => $changeChannel["buyer_zipcode"],
                            "buyerAddress"         => $changeChannel["buyer_address"],
                            "buyerMemo"            => $changeChannel["buyer_memo"],
                        ];
                        $orderChannelDto = new OrderChannelDto();
                        $orderChannelDto->bind($orderChannelDtoBind);
                    }
                    $updateResult = $this->upsertOrderChannelData($orderChannelDto, $orderChannelDetailDtos);
                    if( $updateResult["isSuccess"] == false ){
                        throw new Exception($updateResult["msg"]);
                    }
                }

                /** 2. 추가 주문 update */
                foreach ($addChannels as $addChannel) {
                    $orderChannelDetailDtos = [];

                    if( isset($addChannel["options"]) && !empty($addChannel["options"]) ){
                        $totalQuantity     = 0;
                        $totalPrice        = 0;
                        $totalChannelPrice = 0;
                        foreach ($addChannel["options"] as $option) {
                            $optionQuantity = (int)$option["quantity"];
                            if( $optionQuantity > 0 ){
                                $totalQuantity     += $optionQuantity;
                                $totalPrice        += $optionQuantity * $wOptionPrices[$option["opt_id"]];
                                $totalChannelPrice += $optionQuantity * (int)$option["channel_price"];
    
                                $orderChannelDetailDtoBind = [
                                    "orderChannelId" => 0,
                                    "optionId"       => $option["opt_id"],
                                    "originPrice"    => $wOptionPrices[$option["opt_id"]],
                                    "channelPrice"   => (int)$option["channel_price"],
                                    "quantity"       => $optionQuantity,
                                ];
                                $orderChannelDetailDto = new OrderChannelDetailDto();
                                $orderChannelDetailDto->bind($orderChannelDetailDtoBind);
                
                                $orderChannelDetailDtos[] = $orderChannelDetailDto;
                            }
                        }
                        $orderChannelDtoBind = [
                            "orderId"              => $orderId,
                            "channelOrderId"       => $addChannel["channel_order_id"],
                            "totalQuantity"        => $totalQuantity,
                            "totalPrice"           => $totalPrice,
                            "deliveryPrice"        => $addChannel["delivery_price"],
                            "totalChannelPrice"    => $totalChannelPrice,
                            "buyerName"            => $addChannel["buyer_name"],
                            "buyerClearanceNumber" => $addChannel["buyer_clearance_number"],
                            "buyerNumber"          => $addChannel["buyer_number"],
                            "buyerPhone"           => $addChannel["buyer_phone"],
                            "buyerZipcode"         => $addChannel["buyer_zipcode"],
                            "buyerAddress"         => $addChannel["buyer_address"],
                            "buyerMemo"            => $addChannel["buyer_memo"],
                        ];
                        $orderChannelDto = new OrderChannelDto();
                        $orderChannelDto->bind($orderChannelDtoBind);

                        $updateResult = $this->upsertOrderChannelData($orderChannelDto, $orderChannelDetailDtos);
                        if( $updateResult["isSuccess"] == false ){
                            throw new Exception($updateResult["msg"]);
                        }
                    }
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                throw new Exception($e->getMessage());
            }

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message("error: " . $e->getMessage());
        }

        return $returnMsg;
    }

    /**
    * @func orderInfoChannelDelete
    * @description 'WApp 주문 채널정보 삭제'
    * @param string $orderId
    * @param string $channelOrderId
    * @return array
    */
    public function orderInfoChannelDelete(string $orderId, string $channelOrderId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            DB::beginTransaction();

            $channelObj = OrderChannelData::where([
                "order_id"         => $orderId,
                "channel_order_id" => $channelOrderId
            ])->first();

            if( $channelObj != null ){
                OrderChannelDetailData::where("order_channel_id", $channelObj->id)->forceDelete();
                $channelObj->forceDelete();
            }
            
            DB::commit();
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            DB::rollBack();
            $returnMsg = helpers_fail_message("error: " . $e->getMessage());
        }

        return $returnMsg;
    }
}
