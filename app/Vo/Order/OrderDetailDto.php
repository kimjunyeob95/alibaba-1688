<?php

namespace App\Vo\Order;

use App\Vo\Vo;

class OrderDetailDto extends Vo
{
    protected string $order_id            = "";
    protected int $option_id              = 0;
    protected int $quantity               = 0;
    protected float $origin_option_price  = 0.0;
    protected float $channel_option_price = 0.0;

    public function bind(mixed $data): void
    {
        $this->order_id             = $data["order_id"];
        $this->option_id            = $data["option_id"];
        $this->quantity             = $data["quantity"];
        $this->origin_option_price  = $data["origin_option_price"];
        $this->channel_option_price = $data["channel_option_price"];
       
    }
}