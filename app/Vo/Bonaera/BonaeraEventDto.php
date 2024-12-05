<?php

namespace App\Vo\Bonaera;

use App\Vo\Vo;

class BonaeraEventDto extends Vo
{
    protected string $type           = "";
    protected string $stockNo        = "";
    protected string $groupNo        = "";
    protected string $changeGroupNo  = "";
    protected string $orderId        = "";
    protected string $channelOrderId = "";
    protected array $message         = [];

    public function bind(mixed $data): void
    {
        $this->type           = $data['type'] ?? "";
        $this->stockNo        = $data['stockNo'] ?? "";
        $this->groupNo        = $data['groupNo'] ?? "";
        $this->changeGroupNo  = $data['changeGroupNo'] ?? "";
        $this->orderId        = $data['orderId'] ?? "";
        $this->channelOrderId = $data['channelOrderId'] ?? "";
        $this->message        = $data['message'] ?? [];
    }
}