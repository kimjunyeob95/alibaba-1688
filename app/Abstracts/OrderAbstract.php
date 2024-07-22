<?php

namespace App\Abstracts;

use App\Models\OrderBaseData;
use App\Models\OrderLogisticsData;
use App\Models\OrderProductData;
use App\Models\OrderTradeData;
use App\Vo\Order\OrderDto;
use Exception;

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
    * @description '주문 리스트'
    * @param array $params
    * @return array
    */
    abstract function orderList(array $params): array;

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
    * @description '주문정보 업데이트'
    * @param array $params
    * @return array
    */
    abstract function orderInfoUpdate(array $params): array;

    /**
    * @func upsertOrderBaseData
    * @description '공통 주문정보 upsert'
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
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
