<?php

namespace App\Vo\Product;

use App\Constants\ProductConstant;
use App\Vo\Vo;

class ProductW2Dto extends Vo
{
    protected int $offer_id          = 0;
    protected int $category_id       = 0;
    protected string $status         = "";
    protected string $prd_name       = "";
    protected string $prd_name_en    = "";
    protected string $prd_name_kr    = "";
    protected int $start_quantity    = 1;
    protected string $prd_desc       = "";
    protected string $prd_desc_trans = "";
    protected int $tax_type          = ProductConstant::TAX_TAXATION;
    protected string $minor_not_sale = ProductConstant::MINOR_NOT_SALE_NO;
    protected string $delivery_name  = "데이터 정의 필요";
    protected string $delivery_info  = "데이터 정의 필요";
    protected string $return_comment = ProductConstant::CHANNE_FOREGIN_CHANNEL_RETURN_COMMENT;
    protected int $supply_type       = ProductConstant::SUPP_SEC_2;
    protected int $prd_channel       = ProductConstant::CHANNE_FOREGIN_CHANNEL;
    protected int $prd_rule          = ProductConstant::CHANNE_PRICE_FREE;
    protected string $trans_status   = ProductConstant::TRANS_STATUS_N;
    protected string $mapping_status = ProductConstant::MAPPING_STATUS_N;

    public function bind(mixed $data): void
    {
        $this->offer_id       = $data["offerId"];
        $this->category_id    = $data["categoryId"];
        $this->status         = $data["status"];
        $this->prd_name       = $data["subject"];
        $this->prd_name_en    = $data["subjectEn"];
        $this->prd_name_kr    = $data["subjectKr"];
        $this->start_quantity = $data["startQuantity"];
        $this->prd_desc       = $data["description"];
        $this->mapping_status = $data["mapping_status"];
    }
}