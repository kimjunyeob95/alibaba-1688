<?php

namespace App\Vo\Product;

use App\Constants\ProductConstant;
use App\Vo\Vo;

class Product1688Dto extends Vo
{
    protected int $offer_id           = 0;
    protected string $prd_name        = "";
    protected string $prd_name_trans  = "";
    protected string $prd_desc        = "";
    protected int $tax_type           = ProductConstant::TAX_TAXATION;
    protected string $minor_not_sale  = ProductConstant::MINOR_NOT_SALE_NO;
    protected string $delivery_name   = "데이터 정의 필요";
    protected string $delivery_info   = "데이터 정의 필요";
    protected string $return_comment  = ProductConstant::CHANNE_FOREGIN_CHANNEL_RETURN_COMMENT;
    protected string $supply_type     = ProductConstant::SUPP_SEC_2;
    protected string $prd_channel     = ProductConstant::CHANNE_FOREGIN_CHANNEL;
    protected string $prd_rule        = ProductConstant::CHANNE_PRICE_FREE;
    protected string $main_img        = "";
    protected string $supply_code     = "";

    public function bind(mixed $data): void
    {
        $this->offer_id       = $data["offerId"];
        $this->prd_name       = $data["subject"];
        $this->prd_name_trans = $data["subjectTrans"];
        $this->prd_desc       = $data["description"];
        $this->main_img       = $data["mainImg"];
        $this->supply_code    = $data["offerId"];
    }
}