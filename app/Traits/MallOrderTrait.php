<?php

namespace App\Traits;

use App\Abstracts\OrderAbstract;
use App\Constants\OrderErrorMessageConstant;
use App\Constants\ProductErrorMessageConstant;
use App\Models\OrderBaseData;
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
            $deliveryPrice        = $params["delivery_price"] ?? 0;
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

                    $orderData            = $orderDetailResult["data"]["result"];
                    $orderData["offerId"] = $offerId;
                    $orderData["channel"] = $this->channel;
                    $orderDto             = new OrderDto();
                    $orderDto->bind($orderData);
    
                    $updateResult = $this->orderW1->upsertOrderBaseData($orderDto);
                    if( $updateResult["isSuccess"] == false ){
                        throw new Exception($updateResult["msg"]);
                    }

                    $orderChannelDtoBind = [
                        "orderId"              => $orderId,
                        "channelOrderId"       => $channelOrderId,
                        "totalQuantity"        => $totalQuantity,
                        "totalPrice"           => $totalPrice,
                        "totalChannelPrice"    => $totalChannelPrice,
                        "deliveryPrice"        => $deliveryPrice,
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

                    $updateResult = $this->orderW1->upsertOrderChannelData($orderChannelDto, $orderChannelDetailDtos);
                    if( $updateResult["isSuccess"] == false ){
                        throw new Exception($updateResult["msg"]);
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
