<?php

namespace App\Events;

use App\Vo\Bonaera\OrderPubSubDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPubSubEvent
{
    use Dispatchable, SerializesModels;

    public OrderPubSubDto $orderPubSubDto;

    public function __construct(OrderPubSubDto $orderPubSubDto)
    {
        $this->orderPubSubDto = $orderPubSubDto;
    }
}