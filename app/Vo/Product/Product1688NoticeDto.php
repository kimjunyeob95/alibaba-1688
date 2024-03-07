<?php

namespace App\Vo\Product;

use App\Constants\GosiConstants;
use App\Vo\Vo;

class Product1688NoticeDto extends Vo
{
    protected int $offer_id                 = 0;
    protected int $attribute_id             = 0;
    protected int $notice_type              = GosiConstants::GOSI_CHANNEL_26;
    protected string $attribute_name        = "";
    protected string $attribute_value       = "";
    protected string $attribute_name_trans  = "";
    protected string $attribute_value_trans = "";

    public function bind(mixed $data): void
    {
        $this->offer_id              = $data["offerId"];
        $this->attribute_id          = $data["attributeId"];
        $this->notice_type           = $data["notice_type"];
        $this->attribute_name        = $data["attributeName"];
        $this->attribute_value       = $data["value"];
        $this->attribute_name_trans  = $data["attributeNameTrans"];
        $this->attribute_value_trans = $data["valueTrans"];
    }
}