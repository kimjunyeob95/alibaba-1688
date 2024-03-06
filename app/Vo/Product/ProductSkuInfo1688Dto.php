<?php

namespace App\Vo\Product;

use App\Vo\Vo;

class ProductSkuInfo1688Dto extends Vo
{
    protected int $amount_on_sale   = 0;
    protected float $price          = 0.0;
    protected int $sku_id           = 0;
    protected int $spec_id          = 0;
    protected float $consign_price  = 0.0;
    protected string $cargo_number  = "";
    protected array $sku_attributes = [];

    public function bind(mixed $data): void
    {
        $this->attribute_id          = $data["attributeId"];
        $this->attribute_name        = $data["attributeName"];
        $this->attribute_value       = $data["value"];
        $this->attribute_name_trans  = $data["attributeNameTrans"];
        $this->attribute_value_trans = $data["valueTrans"];
        $this->sku_attributes        = $data["sku_attributes"];
    }
}