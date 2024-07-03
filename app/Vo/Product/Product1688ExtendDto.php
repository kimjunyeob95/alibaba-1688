<?php

namespace App\Vo\Product;

use App\Vo\Vo;

class Product1688ExtendDto extends Vo
{
    protected int $offer_id                       = 0;
    protected int $send_default_price             = 2500;
    protected int $send_jeju_price                = 3000;
    protected int $send_etc_price                 = 3500;
    protected float $trade_medal_level            = 0.0;
    protected float $composite_service_score      = 0.0;
    protected float $logistics_experience_score   = 0.0;
    protected float $dispute_complaint_score      = 0.0;
    protected float $offer_experience_score       = 0.0;
    protected float $consulting_experience_score  = 0.0;
    protected float $trade_score                  = 0.0;
    protected float $repeat_purchase_percent      = 0.0;
    protected float $after_sales_experience_score = 0.0;
    

    public function bind(mixed $data): void
    {
        $this->offer_id                     = $data["offerId"];
        $this->trade_medal_level            = $data["trade_medal_level"];
        $this->composite_service_score      = $data["composite_service_score"];
        $this->logistics_experience_score   = $data["logistics_experience_score"];
        $this->dispute_complaint_score      = $data["dispute_complaint_score"];
        $this->offer_experience_score       = $data["offer_experience_score"];
        $this->consulting_experience_score  = $data["consulting_experience_score"];
        $this->trade_score                  = $data["trade_score"];
        $this->repeat_purchase_percent      = $data["repeat_purchase_percent"];
        $this->after_sales_experience_score = $data["after_sales_experience_score"];
    }
}