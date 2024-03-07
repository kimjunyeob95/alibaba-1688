<?php

namespace App\Vo\Product;

use App\Vo\Vo;

class Product1688ImageDto extends Vo
{
    protected int $offer_id   = 0;
    protected string $img_url = "";

    public function bind(mixed $data): void
    {
        $this->offer_id = $data["offerId"];
        $this->img_url  = $data["imgUrl"];
    }
}