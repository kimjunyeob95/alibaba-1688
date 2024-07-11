<?php

namespace App\Vo\Order;

use App\Vo\Vo;

class OrderDto extends Vo
{
    protected string $order_id = "";
    protected int $offer_id    = 0;
    protected string $channel  = "";
    protected OrderBaseDto $orderBaseDto;
    protected array $orderTradeDtos    = [];
    protected array $orderProductDtos  = [];
    protected array $orderLogisticDtos = [];

    public function bind(mixed $data): void
    {
        $baseInfo = $data["baseInfo"];

        $this->order_id = $baseInfo["id"];
        $this->offer_id = $data["offerId"];
        $this->channel  = $data["channel"];

        $baseInfo["orderId"] = $this->order_id;
        $baseInfo["offerId"] = $this->offer_id;
        $baseInfo["channel"] = $this->channel;
        $this->orderBaseDto  = $this->bindBaseDto($baseInfo);

        $tradeTerms = $data["tradeTerms"];
        foreach ($tradeTerms as $tradeTerm) {
            $tradeTerm["orderId"]   = $this->order_id;
            $tradeTerm["offerId"]   = $this->offer_id;
            $this->orderTradeDtos[] = $this->bindTradeDto($tradeTerm);
        }

        $productItems = $data["productItems"];
        foreach ($productItems as $productItem) {
            $productItem["orderId"]   = $this->order_id;
            $this->orderProductDtos[] = $this->bindProductDto($productItem);
        }

        $logisticsItems = $data["nativeLogistics"]["logisticsItems"] ?? [];
        foreach ($logisticsItems as $logisticsItem) {
            $logisticsItem["orderId"]  = $this->order_id;
            $this->orderLogisticDtos[] = $this->bindLogisticDto($logisticsItem);
        }
    }

    public function bindBaseDto(mixed $data): OrderBaseDto
    {
        $orderBaseDto = new OrderBaseDto();
        $orderBaseDto->bind($data);
        return $orderBaseDto;
    }

    public function bindTradeDto(mixed $data): OrderTradeDto
    {
        $orderTradeDto = new OrderTradeDto();
        $orderTradeDto->bind($data);
        return $orderTradeDto;
    }

    public function bindProductDto(mixed $data): OrderProductDto
    {
        $orderProductDto = new OrderProductDto();
        $orderProductDto->bind($data);
        return $orderProductDto;
    }

    public function bindLogisticDto(mixed $data): OrderLogisticDto
    {
        $orderLogisticDto = new OrderLogisticDto();
        $orderLogisticDto->bind($data);
        return $orderLogisticDto;
    }
}