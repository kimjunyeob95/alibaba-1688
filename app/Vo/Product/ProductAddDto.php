<?php

namespace App\Vo\Product;

use App\Vo\Vo;

class ProductAddDto extends Vo
{
    protected int $offer_id                   = 0;
    protected int $top_category_id            = 0;
    protected int $second_category_id         = 0;
    protected int $third_category_id          = 0;
    protected string $main_video              = "";
    protected string $detail_video            = "";
    protected int $min_order_quantity         = 1;
    protected string $shipping_time_guarantee = "";

    public function bind(mixed $data): void
    {
        $this->offer_id                = $data["offerId"];
        $this->top_category_id         = $data["topCategoryId"];
        $this->second_category_id      = $data["secondCategoryId"];
        $this->third_category_id       = $data["thirdCategoryId"];
        $this->main_video              = $data["mainVideo"];
        $this->detail_video            = $data["detailVideo"];
        $this->min_order_quantity      = $data["minOrderQuantity"];
        $this->shipping_time_guarantee = $data["shippingTimeGuarantee"];
    }
}