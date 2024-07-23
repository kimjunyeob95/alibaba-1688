<?php

namespace App\Abstracts;

use App\Models\OrderBaseData;
use App\Models\OrderChannelData;
use App\Models\OrderChannelDetailData;
use App\Models\OrderLogisticsData;
use App\Models\OrderProductData;
use App\Models\OrderTradeData;
use App\Vo\Order\OrderChannelDto;
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
                "channel_obj",
                "w_options.option"
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
}
