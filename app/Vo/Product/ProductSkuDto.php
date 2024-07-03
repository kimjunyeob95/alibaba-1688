<?php

namespace App\Vo\Product;

use App\Vo\Vo;

class ProductSkuDto extends Vo
{
    protected int $offer_id          = 0;
    protected int $sku_id            = 0;
    protected float $price           = 0;
    protected float $jxhy_price      = 0;
    protected float $pf_jxhy_price   = 0;
    protected float $consign_price   = 0;
    protected float $promotion_price = 0;

    public function bind(mixed $data): void
    {
        $this->offer_id        = $data["offerId"];
        $this->sku_id          = $data["skuId"];
        $this->price           = $data["price"];
        $this->jxhy_price      = $data["jxhyPrice"];
        $this->pf_jxhy_price   = $data["pfJxhyPrice"];
        $this->consign_price   = $data["consignPrice"];
        $this->promotion_price = $data["promotionPrice"];
    }
}