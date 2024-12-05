<?php

namespace App\Vo\Order;

use App\Vo\Vo;

class OrderPubSubDto extends Vo
{
    protected string $type    = "";
    protected string $orderId = "";
    protected array $message  = [];

    public function bind(mixed $data): void
    {
        $this->type    = $data['type'] ?? "";
        $this->orderId = $data['orderId'] ?? "";
        $this->message = $data['message'] ?? [];
    }
}