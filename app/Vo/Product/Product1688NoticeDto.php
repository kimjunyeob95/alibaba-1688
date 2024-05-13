<?php

namespace App\Vo\Product;

use App\Constants\GosiConstants;
use App\Vo\Vo;

class Product1688NoticeDto extends Vo
{
    protected int $offer_id              = 0;
    protected int $attribute_id          = 0;
    protected int $notice_type           = GosiConstants::GOSI_CHANNEL_26;
    protected string $is_except          = GosiConstants::IS_EXCEPT_N;
    protected string $attribute_name     = "";
    protected string $attribute_value    = "";
    protected string $attribute_name_kr  = "";
    protected string $attribute_value_kr = "";
    protected string $attribute_name_en  = "";
    protected string $attribute_value_en = "";

    public function bind(mixed $data): void
    {
        $this->offer_id           = $data["offerId"];
        $this->attribute_id       = $data["attributeId"];
        $this->is_except          = $data["is_except"];
        $this->attribute_name     = $data["attributeName"];
        $this->attribute_value    = $data["value"];
        $this->attribute_name_kr  = $data["attributeNameTrans"];
        $this->attribute_value_kr = $data["valueTrans"];
        $this->attribute_name_en  = $data["attributeNameTransEn"];
        $this->attribute_value_en = $data["valueTransEn"];
    }
}