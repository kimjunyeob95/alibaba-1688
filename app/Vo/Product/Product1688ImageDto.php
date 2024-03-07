<?php

namespace App\Vo\Product;

use App\Vo\Vo;

class Product1688ImageDto extends Vo
{
    protected int $offer_id          = 0;
    protected string $img_url_origin = "";
    protected string $img_url_trans  = "";

    public function bind(mixed $data): void
    {
        $this->offer_id       = $data["offerId"];
        $this->img_url_origin = $data["img_url_origin"];
        $this->img_url_trans  = $data["img_url_trans"];
    }
}