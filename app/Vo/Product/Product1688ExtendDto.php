<?php

namespace App\Vo\Product;

use App\Vo\Vo;

class Product1688ExtendDto extends Vo
{
    protected int $offer_id           = 0;
    protected int $send_default_price = 2500;
    protected int $send_jeju_price    = 3000;
    protected int $send_etc_price     = 3500;

    public function bind(mixed $data): void
    {
        $this->offer_id = $data["offerId"];
    }
}