<?php

namespace App\Vo\Product;

use App\Constants\ImageConstant;
use App\Constants\WConstant;
use App\Vo\Vo;

class Product1688ImageDto extends Vo
{
    protected int $offer_id          = 0;
    protected string $img_type       = ImageConstant::IMAGE_TYPE_MAIN;
    protected string $lang           = WConstant::WAPP_KR;
    protected string $is_except      = ImageConstant::IS_EXCEPT_N;
    protected string $img_url_origin = "";
    protected string $img_url_trans  = "";
    protected string $trans_dated_at = "";
    protected bool $is_change_img    = false;
    protected int $width             = 0;
    protected int $height            = 0;
    protected int $byte              = 0;
    protected string $mime           = "";

    public function bind(mixed $data): void
    {
        $this->offer_id       = $data["offerId"];
        $this->img_type       = $data["imgType"];
        $this->lang           = $data["lang"];
        $this->is_except      = $data["is_except"];
        $this->img_url_origin = $data["img_url_origin"];
        $this->img_url_trans  = $data["img_url_trans"];
        $this->is_change_img  = $data["isChangeImg"];

        if( $data["isChangeImg"] == true ){
            $this->bind_detail($data);
        }
    }

    public function bind_detail(mixed $data): void
    {
        $this->width  = $data["width"];
        $this->height = $data["height"];
        $this->byte   = $data["byte"];
        $this->mime   = $data["mime"];
    }
}