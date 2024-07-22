<?php

namespace App\Vo\Product;

use App\Vo\Vo;

class ProductChannelPriceDto extends Vo
{
    protected int $offer_id         = 0;
    protected int $sku_id           = 0;
    protected string $current_price = "";

    public function bind(mixed $data): void
    {
        $this->offer_id      = $data["offerId"];
        $this->sku_id        = $data["skuId"];
        $this->current_price = $data["currentPrice"];
    }
}