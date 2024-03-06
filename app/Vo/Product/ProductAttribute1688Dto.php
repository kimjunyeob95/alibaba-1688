<?php

namespace App\Vo\Product;

use App\Vo\Vo;

class ProductAttribute1688Dto extends Vo
{
    protected int $attribute_id             = 0;
    protected string $attribute_name        = "";
    protected string $attribute_value       = "";
    protected string $attribute_name_trans  = "";
    protected string $attribute_value_trans = "";
    protected array $sku_attributes         = [];


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