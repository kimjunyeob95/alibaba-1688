<?php

namespace App\Traits;

use App\Abstracts\OrderAbstract;
use App\Constants\OrderErrorMessageConstant;
use App\Constants\ProductErrorMessageConstant;
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

trait MallOrderTrait
{
    protected array $returnMsg;
    protected string $channel;
    protected OrderAbstract $orderW1;
    
    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }
    
    public function initOrderTrait(string $channel, OrderAbstract $orderW1): void
    {
        $this->channel = $channel;
        $this->orderW1 = $orderW1;
    }

    /**
     * @func orderInfo
     * @description '주문 조회'
     * @param string $orderId
     * @return array
    */
    public function orderInfo(string $orderId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $orderObj = OrderBaseData::where([
                "channel"  => $this->channel,
                "order_id" => $orderId,
            ]);

            if( $orderObj == null ){
                throw new Exception(OrderErrorMessageConstant::getNotHaveErrorMessage("ORDER"));
            }

            $result = $this->orderW1->getWOrder($orderId);
            if( $result["isSuccess"] === true && isset($result["data"]["result"]) ){
                $returnMsg = helpers_success_message($result["data"]["result"]);
            } else {
                $returnMsg = helpers_fail_message($result["msg"]);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func orderCreate
     * @description '주문 생성'
     * @param array $params
     * @return array
     */
    public function orderCreate(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $offerId              = $params["offer_id"];
            $channelOrderId       = $params["channel_order_id"];
            $optionParamList      = $params["option_param_list"];
            $buyerName            = $params["buyer_name"];
            $buyerClearanceNumber = $params["buyer_clearance_number"];
            $buyerNumber          = $params["buyer_number"];
            $buyerPhone           = $params["buyer_phone"];
            $buyerZipcode         = $params["buyer_zipcode"];
            $buyerAddress         = $params["buyer_address"];
            $buyerMemo            = $params["buyer_memo"];
            $totalQuantity        = 0;
            $totalPrice           = 0.0;
            $totalChannelPrice    = 0.0;

            $orderChannelDetailDtos = [];

            foreach ($optionParamList as &$option) {
                $optionId = $option["option_id"];
                $quantity = $option["quantity"];
                $price    = $option["price"];

                $optObj = ProductOptionData::where([
                    "id"       => $optionId,
                    "offer_id" => $offerId,
                ])->first();
                if( $optObj == null ){
                    throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("OPTION"));
                }

                if( $quantity < 1 ){
                    throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("OPTION_QUANTITY"));
                }

                $option["specId"] = $optObj->spec_id;

                $totalQuantity     += $quantity;
                $totalPrice         = $totalPrice + ( $optObj->price_1688_option * $quantity );
                $totalChannelPrice  = $totalChannelPrice + ( $price * $quantity );

                $orderChannelDetailDtoBind = [
                    "orderChannelId" => 0,
                    "optionId"       => $optObj->id,
                    "originPrice"    => $optObj->price_1688_option,
                    "channelPrice"   => $price,
                    "quantity"       => $quantity,
                ];
                $orderChannelDetailDto = new OrderChannelDetailDto();
                $orderChannelDetailDto->bind($orderChannelDetailDtoBind);

                $orderChannelDetailDtos[] = $orderChannelDetailDto;
            }

            if( $totalQuantity < 1 ){
                throw new Exception(OrderErrorMessageConstant::getFitErrorMessage("TOTAL_QUANTITY"));
            }

            $payload = [
                "offerId"         => $offerId,
                "optionParamList" => $optionParamList
            ];
            $result = $this->orderW1->createWOrder($payload, $totalQuantity);
            if( $result["isSuccess"] === true && isset($result["data"]["orderId"]) ){
                $orderId           = $result["data"]["orderId"];
                $orderDetailResult = $this->orderW1->getWOrder($orderId);
                if( $orderDetailResult["isSuccess"] == false || 
                    !isset($orderDetailResult["data"]["result"]["baseInfo"]) ||
                    empty($orderDetailResult["data"]["result"]["baseInfo"])
                ){
                    throw new Exception(OrderErrorMessageConstant::getFitErrorMessage("W_DETAIL"));
                }

                try {
                    DB::beginTransaction();

                    $orderData = $orderDetailResult["data"]["result"];
                    $orderData["offerId"] = $offerId;
                    $orderData["channel"] = $this->channel;
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

                    $orderChannelDtoBind = [
                        "orderId"              => $orderId,
                        "channelOrderId"       => $channelOrderId,
                        "totalQuantity"        => $totalQuantity,
                        "totalPrice"           => $totalPrice,
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

                    $returnPayload = [
                        "order_id"         => $orderId,
                        "channel_order_id" => $channelOrderId,
                        "success"          => $result["data"]["success"],
                    ];

                    $returnMsg = helpers_success_message($returnPayload);
                } catch (Exception $ee) {
                    DB::rollBack();
                    $returnMsg = helpers_fail_message($ee->getMessage());
                }
            } else {
                $returnMsg = helpers_fail_message($result["msg"], $result);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
