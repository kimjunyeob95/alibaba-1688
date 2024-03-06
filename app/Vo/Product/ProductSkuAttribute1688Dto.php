<?php

namespace App\Vo\Product;

use App\Vo\Vo;

class ProductSkuAttribute1688Dto extends Vo
{
    protected int $attribute_id           = 0;
    protected string $attribute_name      = "";
    protected string $attributeName_trans = "";
    protected string $value               = "";
    protected string $value_trans         = "";
    protected string $sku_image_url       = "";

    public function bind(mixed $data): void
    {
        $this->attribute_id        = $data["attributeId"];
        $this->attribute_name      = $data["attributeName"];
        $this->attributeName_trans = $data["attributeNameTrans"];
        $this->value               = $data["value"];
        $this->value_trans         = $data["valueTrans"];
        $this->sku_image_url       = $data["skuImageUrl"] ?? "";
    }
}