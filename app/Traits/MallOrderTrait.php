<?php

namespace App\Traits;

use App\Abstracts\OrderAbstract;
use App\Constants\OrderErrorMessageConstant;
use App\Constants\ProductErrorMessageConstant;
use App\Models\OrderData;
use App\Models\OrderDetailData;
use App\Models\ProductData;
use App\Models\ProductOptionData;
use App\Vo\Order\OrderDetailDto;
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
            $orderObj = OrderData::where([
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
            $optionParamList      = $params["optionParamList"];
            $optionPrice          = $params["option_price"];
            $buyerName            = $params["buyer_name"];
            $buyerClearanceNumber = $params["buyer_clearance_number"];
            $buyerNumber          = $params["buyer_number"];
            $buyerPhone           = $params["buyer_phone"];
            $buyerZipcode         = $params["buyer_zipcode"];
            $buyerAddress         = $params["buyer_address"];
            $buyerMemo            = $params["buyer_memo"];
            $totalQuantity        = 0;

            $orderDetailDtos = [];

            foreach ($optionParamList as &$option) {
                $optionId = $option["option_id"];
                $quantity = $option["quantity"];

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
                $totalQuantity += $quantity;

                $orderDetailDtoBind = [
                    "order_id"             => 0,
                    "option_id"            => $optObj->id,
                    "quantity"             => $quantity,
                    "origin_option_price"  => $optObj->option_price,
                    "channel_option_price" => $optionPrice,
                ];
                $orderDetailDto = new OrderDetailDto();
                $orderDetailDto->bind($orderDetailDtoBind);

                $orderDetailDtos[] = $orderDetailDto;
            }

            $payload = [
                "offerId"         => $offerId,
                "optionParamList" => $optionParamList
            ];
            $result = $this->orderW1->createWOrder($payload, $totalQuantity);

            if( $result["isSuccess"] === true && isset($result["data"]["orderId"]) ){
                $orderId = $result["data"]["orderId"];

                try {
                    DB::beginTransaction();

                    $orderDtoBind = [
                        "order_id"               => $orderId,
                        "offer_id"               => $offerId,
                        "channel"                => $this->channel,
                        "buyer_name"             => $buyerName,
                        "buyer_clearance_number" => $buyerClearanceNumber,
                        "buyer_number"           => $buyerNumber,
                        "buyer_phone"            => $buyerPhone,
                        "buyer_zipcode"          => $buyerZipcode,
                        "buyer_address"          => $buyerAddress,
                        "buyer_memo"             => $buyerMemo,
                        "total_quantity"         => $totalQuantity,
                        "total_price"            => $totalQuantity * $optionPrice
                    ];
                    $orderDto = new OrderDto();
                    $orderDto->bind($orderDtoBind);

                    $odProperties = $orderDto->getAllProperties();
                    OrderData::create($odProperties);

                    foreach ($orderDetailDtos as $orderDetailDto) {
                        $orderDetailDto->order_id = $orderId;

                        $oddProperties = $orderDetailDto->getAllProperties();
                        OrderDetailData::create($oddProperties);
                    }
    
                    DB::commit();

                    $returnPayload = [
                        "order_id" => $orderId,
                        "success"  => $result["data"]["success"],
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
