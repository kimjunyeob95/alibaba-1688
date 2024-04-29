<?php

namespace App\Vo\Product;

use App\Vo\Vo;

class Product1688OptionDto extends Vo
{
    protected int $offer_id             = 0;
    protected int $sku_id               = 0;
    protected string $spec_id           = "";
    protected string $status            = "";
    protected string $option_name       = "";
    protected string $option_name_trans = "";
    protected float $price_1688         = 0.0;
    protected float $option_price       = 0.0;
    protected float $md_price           = 0.0;
    protected float $onch_price         = 0.0;
    protected float $cus_price          = 0.0;
    protected float $recom_cus_price    = 0.0;
    protected int $amount_on_sale       = 0;
    protected string $cargo_number      = "";
    protected float $exchange_rate      = 0.0;

    public function bind(mixed $data): void
    {
        $this->offer_id          = $data["offerId"];
        $this->sku_id            = $data["skuId"];
        $this->spec_id           = $data["specId"];
        $this->status            = $data["status"];
        $this->option_name       = $data["optionName"];
        $this->option_name_trans = $data["optionNameTrans"];
        $this->price_1688        = (float)$data["price_1688"];
        $this->md_price          = isset($data["md_price"]) ? (int)$data["md_price"] : 0;
        $this->amount_on_sale    = $data["amountOnSale"];
        $this->cargo_number      = $data["cargoNumber"];
        $this->exchange_rate     = env("1688_EXCHANGE_RATE", 200);

        $this->oc_bind();
    }

    public function oc_bind(): void
    {
        $this->option_price = round( $this->price_1688 * $this->exchange_rate, -1);  // 1의 자리 반올림

        $option_price_sum = (int)intval($this->option_price) + intval($this->option_price * env("OPTION_PRICE_RATE", 0.12));
        $option_price_cal = round($option_price_sum / 10) * 10;
        $this->onch_price = $option_price_cal;

        $recom_cus_price_sum = (int)intval($this->option_price) + intval($this->option_price * env("RECOM_CUS_PRICE_RATE", 0.45));
        $recom_cus_price_cal = round($recom_cus_price_sum / 10) * 10;
        $this->cus_price       = $recom_cus_price_cal;
        $this->recom_cus_price = $recom_cus_price_cal;
    }
}