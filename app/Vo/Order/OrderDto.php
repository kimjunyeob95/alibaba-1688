<?php

namespace App\Vo\Order;

use App\Vo\Vo;

class OrderDto extends Vo
{
    protected int $order_id                  = 0;
    protected int $offer_id                  = 0;
    protected string $channel                = "";
    protected string $buyer_name             = "";
    protected string $buyer_clearance_number = "";
    protected string $buyer_number           = "";
    protected string $buyer_phone            = "";
    protected string $buyer_zipcode          = "";
    protected string $buyer_address          = "";
    protected string $buyer_memo             = "";
    protected int $total_quantity            = 0;
    protected float $total_price             = 0.0;

    public function bind(mixed $data): void
    {
        $this->order_id               = $data["order_id"];
        $this->offer_id               = $data["offer_id"];
        $this->channel                = $data["channel"];
        $this->buyer_name             = $data["buyer_name"];
        $this->buyer_clearance_number = $data["buyer_clearance_number"];
        $this->buyer_number           = $data["buyer_number"];
        $this->buyer_phone            = $data["buyer_phone"];
        $this->buyer_zipcode          = $data["buyer_zipcode"];
        $this->buyer_address          = $data["buyer_address"];
        $this->buyer_memo             = $data["buyer_memo"];
        $this->total_quantity         = $data["total_quantity"];
        $this->total_price            = $data["total_price"];
    }
}