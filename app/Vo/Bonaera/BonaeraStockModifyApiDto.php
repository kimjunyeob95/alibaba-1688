<?php

namespace App\Vo\Bonaera;

use App\Vo\Vo;

class BonaeraStockModifyApiDto extends Vo
{
    protected string $itCode         = "";
    protected string $productShno    = "";
    protected string $productNameEng = "";
    protected string $trackingNumber = "";
    protected float $productMoney    = 0.0;
    protected int $productCount      = 0;
    protected string $imgUrl         = "";
    protected string $option1        = "";
    protected string $option2        = "";

    public function bind(mixed $data): void
    {
        $this->itCode         = $data["itCode"];
        $this->productShno    = $data["productShno"];
        $this->productNameEng = $data["productNameEng"];
        $this->trackingNumber = $data["trackingNumber"];
        $this->productMoney   = $data["productMoney"];
        $this->productCount   = $data["productCount"];
        $this->imgUrl         = $data["imgUrl"];
        $this->option1        = $data["option1"];
        $this->option2        = $data["option2"];
    }
}