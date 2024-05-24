<?php

namespace App\Vo\Product;

use App\Constants\ProductConstant;
use App\Constants\WConstant;
use App\Vo\Vo;

class Product1688Dto extends Vo
{
    protected int $offer_id           = 0;
    protected int $category_id        = 0;
    protected string $status          = "";
    protected string $w_type          = WConstant::WAPP_W1;
    protected string $prd_name        = "";
    protected string $prd_name_kr     = "";
    protected string $prd_name_en     = "";
    protected int $start_quantity     = 1;
    protected string $prd_desc        = "";
    protected string $prd_desc_kr     = "";
    protected string $prd_desc_en     = "";
    protected int $tax_type           = ProductConstant::TAX_TAXATION;
    protected string $minor_not_sale  = ProductConstant::MINOR_NOT_SALE_NO;
    protected string $delivery_name   = "데이터 정의 필요";
    protected string $delivery_info   = "데이터 정의 필요";
    protected string $return_comment  = ProductConstant::CHANNE_FOREGIN_CHANNEL_RETURN_COMMENT;
    protected int $supply_type        = ProductConstant::SUPP_SEC_2;
    protected int $prd_channel        = ProductConstant::CHANNE_FOREGIN_CHANNEL;
    protected int $prd_rule           = ProductConstant::CHANNE_PRICE_FREE;
    protected int $sold_out           = 0;
    protected string $trans_status    = ProductConstant::TRANS_STATUS_N;
    protected string $trans_status_en = ProductConstant::TRANS_STATUS_N;
    protected string $mapping_status  = ProductConstant::MAPPING_STATUS_N;
    protected string $inspect_status  = ProductConstant::INSPECT_STATUS_N;

    public function bind(mixed $data): void
    {
        $this->offer_id       = $data["offerId"];
        $this->category_id    = $data["categoryId"];
        $this->status         = $data["status"];
        $this->w_type         = $data["wType"];
        $this->prd_name       = $data["subject"];
        $this->prd_name_kr    = $data["subjectTrans"];
        $this->prd_name_en    = $data["subjectTransEn"];
        $this->start_quantity = $data["startQuantity"];
        $this->prd_desc       = $data["description"];
        $this->prd_desc_kr    = $data["prdDescKr"];
        $this->prd_desc_en    = $data["prdDescEn"];
        $this->sold_out       = $data["soldOut"];
        $this->mapping_status = $data["mapping_status"];
        $this->inspect_status = $data["inspect_status"];
    }
}