<?php

namespace App\Vo\Product;

use App\Constants\OptionConstants;
use App\Vo\Vo;

class Product1688OptionDto extends Vo
{
    protected int $offer_id                   = 0;
    protected int $sku_id                     = 0;
    protected string $spec_id                 = "";
    protected string $status                  = "";
    protected string $is_except               = OptionConstants::IS_EXCEPT_N;
    protected string $option_name             = "";
    protected string $option_name_kr          = "";
    protected string $option_name_en          = "";
    protected string $sku_img_url             = "";
    protected float $price_1688               = 0.0;
    protected float $price_1688_option        = 0.0;
    protected float $option_price             = 0.0;
    protected float $md_price                 = 0.0;
    protected float $onch_price               = 0.0;
    protected float $cus_price                = 0.0;
    protected float $recom_cus_price          = 0.0;
    protected int $amount_on_sale             = 0;
    protected string $cargo_number            = "";
    protected float $exchange_rate            = 0.0;
    protected float $width                    = 0.0;
    protected float $length                   = 0.0;
    protected float $height                   = 0.0;
    protected float $weight                   = 0.0;
    protected string $send_goods_address_text = "";
    protected string $pkg_size_source         = "";

    public function bind(mixed $data): void
    {
        $this->offer_id                = $data["offerId"];
        $this->sku_id                  = $data["skuId"];
        $this->spec_id                 = $data["specId"];
        $this->status                  = $data["status"];
        $this->is_except               = $data["is_except"];
        $this->option_name             = $data["optionName"];
        $this->option_name_kr          = $data["optionNameTrans"];
        $this->option_name_en          = $data["optionNameTransEn"];
        $this->sku_img_url             = $data["skuImageUrl"];
        $this->price_1688              = (float)$data["price_1688"];
        $this->price_1688_option       = (float)$data["price_1688_option"];
        $this->md_price                = isset($data["md_price"]) ? (int)$data["md_price"] : 0;
        $this->amount_on_sale          = $data["amountOnSale"];
        $this->cargo_number            = $data["cargoNumber"];
        $this->width                   = $data["width"];
        $this->length                  = $data["length"];
        $this->height                  = $data["height"];
        $this->weight                  = $data["weight"];
        $this->send_goods_address_text = $data["send_goods_address_text"];
        $this->pkg_size_source         = $data["pkg_size_source"];

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