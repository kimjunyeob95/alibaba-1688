<?php

namespace App\Vo\Product;

use App\Constants\ProductConstant;
use App\Vo\Vo;

class Product1688OptionDto extends Vo
{
    protected int $offer_id             = 0;
    protected int $amount_on_sale       = 0;
    protected int $sku_id               = 0;
    protected string $spec_id           = "";
    protected string $sku_image_url     = "";
    protected string $option_name       = "";
    protected string $option_name_trans = "";
    protected float $price_1688         = 0.0;
    protected float $consign_price      = 0.0;
    protected string $cargo_number      = "";
    protected float $option_price       = 0.0;
    protected float $onch_price         = 0.0;
    protected float $cus_price          = 0.0;
    protected float $recom_cus_price    = 0.0;
    protected string $status               = ProductConstant::OPTION_SEC_ON_SALE_NUMBER;

    public function bind(mixed $data): void
    {
        $this->offer_id          = $data["offerId"];
        $this->amount_on_sale    = $data["amountOnSale"];
        $this->sku_id            = $data["skuId"];
        $this->spec_id           = $data["specId"];
        $this->sku_image_url     = $data["skuImageUrl"];
        $this->option_name       = $data["optionName"];
        $this->option_name_trans = $data["optionNameTrans"];
        $this->price_1688        = $data["price"];
        $this->consign_price     = (float)$data["consignPrice"];
        $this->cargo_number      = $data["cargoNumber"];

        $this->oc_bind();
    }

    public function oc_bind(): void
    {
        $this->onch_price   = round( $this->price_1688 * env("1688_EXCHANGE_RATE", 190) , -1);  // 1의 자리 반올림

        $option_price_sum = (int)intval($this->onch_price) + intval($this->onch_price * env("OPTION_PRICE_RATE", 0.12));
        $option_price_cal = round($option_price_sum / 10) * 10;
        $this->option_price = $option_price_cal;

        $recom_cus_price_sum = (int)intval($this->onch_price) + intval($this->onch_price * env("RECOM_CUS_PRICE_RATE", 0.45));
        $recom_cus_price_cal = round($recom_cus_price_sum / 10) * 10;
        $this->cus_price       = $recom_cus_price_cal;
        $this->recom_cus_price = $recom_cus_price_cal;
    }
}