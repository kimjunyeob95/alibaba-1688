<?php

namespace App\Vo\Product;

use App\Constants\ProductConstant;
use App\Vo\Vo;

class Product1688Dto extends Vo
{
    protected int $offer_id           = 0;
    protected int $category_id        = 0;
    protected string $prd_name        = "";
    protected string $prd_name_trans  = "";
    protected string $prd_desc        = "";
    protected int $tax_type           = ProductConstant::TAX_TAXATION;
    protected string $minor_not_sale  = ProductConstant::MINOR_NOT_SALE_NO;
    protected string $delivery_name   = "";
    protected string $delivery_info   = "";
    protected int $send_default_price = 2500;
    protected int $send_jeju_price    = 3000;
    protected int $send_etc_price     = 3500;
    protected string $return_comment  = "";
    protected string $supply_type     = ProductConstant::SUPP_SEC_2;
    protected string $prd_channel     = ProductConstant::CHANNE_FOREGIN_CHANNEL;
    protected string $prd_rule     = ProductConstant::CHANNE_PRICE_FOLLOW;

    public function bind(mixed $data): void
    {
        
    }
}