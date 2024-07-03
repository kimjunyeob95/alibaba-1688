<?php

namespace App\Vo\Product;

use App\Vo\Vo;

class ProductSaleDto extends Vo
{
    protected int $offer_id          = 0;
    protected int $amount_on_sale    = 0;
    protected int $start_quantity    = 1;
    protected float $price           = 0;
    protected int $quote_type        = 0;
    protected float $promotion_price = 0;
    protected float $consign_price   = 0;
    protected float $jxhy_price      = 0;

    public function bind(mixed $data): void
    {
        $this->offer_id        = $data["offerId"];
        $this->amount_on_sale  = $data["amountOnSale"];
        $this->start_quantity  = $data["startQuantity"];
        $this->price           = $data["price"];
        $this->quote_type      = $data["quoteType"];
        $this->promotion_price = $data["promotionPrice"];
        $this->consign_price   = $data["consignPrice"];
        $this->jxhy_price      = $data["jxhyPrice"];
    }
}