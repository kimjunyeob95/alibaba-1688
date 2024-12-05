<?php

namespace App\Vo\Wms;

use App\Vo\Vo;

class WmsPubSubDto extends Vo
{
    protected string $type           = "";
    protected string $stockNo        = "";
    protected string $groupNo        = "";
    protected string $changeGroupNo  = "";
    protected string $orderId        = "";
    protected string $channelOrderId = "";

    public function bind(mixed $data): void
    {
        $this->type           = $data['type'] ?? "";
        $this->stockNo        = $data['stockNo'] ?? "";
        $this->groupNo        = $data['groupNo'] ?? "";
        $this->changeGroupNo  = $data['changeGroupNo'] ?? "";
        $this->orderId        = $data['orderId'] ?? "";
        $this->channelOrderId = $data['channelOrderId'] ?? "";
    }
}