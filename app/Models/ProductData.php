<?php

namespace App\Models;

use App\Constants\EasySellConstant;
use App\Constants\ForbiddenWordConstant;
use App\Constants\GosiConstants;
use App\Constants\ImageConstant;
use App\Constants\InspectConstant;
use App\Constants\OnchannelConstant;
use App\Constants\OptionConstant;
use App\Constants\ProductConstant;
use App\Constants\WConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'product_datas';
    protected $guarded    = [];
    protected $fillable   = [];

    public function main_img () {
        return $this->hasOne(ProductImageData::class, "offer_id", "offer_id")->where("lang", WConstant::WAPP_KR)->where("img_type", ImageConstant::IMAGE_TYPE_MAIN);
    }

    public function en_main_img () {
        return $this->hasOne(ProductImageData::class, "offer_id", "offer_id")->where("lang", WConstant::WAPP_EN)->where("img_type", ImageConstant::IMAGE_TYPE_MAIN);
    }

    public function sub_imgs () {
        return $this->hasMany(ProductImageData::class, "offer_id", "offer_id")->where("lang", WConstant::WAPP_KR)->where("img_type", ImageConstant::IMAGE_TYPE_SUB);
    }

    public function no_except_sub_imgs () {
        return $this->hasMany(ProductImageData::class, "offer_id", "offer_id")->where("lang", WConstant::WAPP_KR)->where("img_type", ImageConstant::IMAGE_TYPE_SUB)->where("is_except", ImageConstant::IS_EXCEPT_N);
    }

    public function en_sub_imgs () {
        return $this->hasMany(ProductImageData::class, "offer_id", "offer_id")->where("lang", WConstant::WAPP_EN)->where("img_type", ImageConstant::IMAGE_TYPE_SUB);
    }

    public function no_except_en_sub_imgs () {
        return $this->hasMany(ProductImageData::class, "offer_id", "offer_id")->where("lang", WConstant::WAPP_EN)->where("img_type", ImageConstant::IMAGE_TYPE_SUB)->where("is_except", ImageConstant::IS_EXCEPT_N);
    }

    public function desc_imgs () {
        return $this->hasMany(ProductImageData::class, "offer_id", "offer_id")->where("lang", WConstant::WAPP_KR)->where("img_type", ImageConstant::IMAGE_TYPE_DESC);
    }

    public function en_desc_imgs () {
        return $this->hasMany(ProductImageData::class, "offer_id", "offer_id")->where("lang", WConstant::WAPP_EN)->where("img_type", ImageConstant::IMAGE_TYPE_DESC);
    }

    public function images () {
        return $this->hasMany(ProductImageData::class, "offer_id", "offer_id")->where("lang", WConstant::WAPP_KR)->where("img_type", "!=", ImageConstant::IMAGE_TYPE_WHITE)->orderBy('img_type', 'asc')->orderBy('id', 'asc');
    }

    public function en_images () {
        return $this->hasMany(ProductImageData::class, "offer_id", "offer_id")->where("lang", WConstant::WAPP_EN)->orderBy('img_type', 'asc')->orderBy('id', 'asc');
    }

    public function no_except_en_images () {
        return $this->hasMany(ProductImageData::class, "offer_id", "offer_id")->where("lang", WConstant::WAPP_EN)->where("is_except", ImageConstant::IS_EXCEPT_N)->orderBy('img_type', 'asc')->orderBy('id', 'asc');
    }

    public function options () {
        return $this->hasMany(ProductOptionData::class, "offer_id", "offer_id")->oldest("id");
    }

    public function no_except_options () {
        return $this->hasMany(ProductOptionData::class, "offer_id", "offer_id")->where("is_except", OptionConstant::IS_EXCEPT_N);
    }

    public function extends () {
        return $this->hasOne(ProductExtendData::class, "offer_id", "offer_id")->oldest("id");
    }

    public function notices () {
        return $this->hasMany(ProductNoticeData::class, "offer_id", "offer_id")->oldest("id");
    }

    public function no_except_notices () {
        return $this->hasMany(ProductNoticeData::class, "offer_id", "offer_id")->where("is_except", GosiConstants::IS_EXCEPT_N);
    }

    public function category () {
        return $this->hasOne(CategoryTree::class, "category_id", "category_id");
    }

    public function w_mapping () {
        return $this->hasOne(CategoryMapping::class, "category_id", "category_id")->where("mapping_channel", ProductConstant::MAPPING_WAPP);
    }

    public function w_category () {
        return $this->hasOne(WCategory::class, "mapping_code", "w_mapping_code");
    }

    public function oc_mapping () {
        return $this->hasOne(CategoryMapping::class, "category_id", "category_id")->where("mapping_channel", ProductConstant::MAPPING_OC_CHANNEL);
    }

    public function oc_category()
    {
        return $this->hasOne(OnchCategoryExcelDataCopy2::class, "codenum", "channel_mapping_code");
    }

    public function es_mapping () {
        return $this->hasOne(CategoryMapping::class, "category_id", "category_id")->where("mapping_channel", ProductConstant::MAPPING_ES_CHANNEL);
    }

    public function es_fgn_mapping () {
        return $this->hasOne(CategoryMapping::class, "category_id", "category_id")->where("mapping_channel", ProductConstant::MAPPING_ES_FGN_CHANNEL);
    }

    public function es_category()
    {
        return $this->hasOne(SellerhubCategory::class, "sellerhub_cate", "es_fgn_mapping");
    }

    public function easysell () {
        return $this->hasOne(EasysellProductLog::class, "offer_id", "offer_id");
    }

    public function onchannel () {
        return $this->hasOne(OnchannelProductLog::class, "offer_id", "offer_id");
    }

    public function img_inspect () {
        return $this->hasOne(ProductInspectData::class, "offer_id", "offer_id")->where("inspect_type", InspectConstant::INSPECT_IMAGE);
    }

    public function prd_inspect () {
        return $this->hasOne(ProductInspectData::class, "offer_id", "offer_id")->where("inspect_type", InspectConstant::INSPECT_PRODUCT);
    }

    public function gosi_inspect () {
        return $this->hasOne(ProductInspectData::class, "offer_id", "offer_id")->where("inspect_type", InspectConstant::INSPECT_NOTICE);
    }

    public function weight_delivery () {
        return $this->hasOne(ProductWeightData::class, "offer_id", "offer_id");
    }

    public function es_w_log () {
        return $this->hasOne(EasysellProductLog::class, "offer_id", "offer_id")->where("w_type", EasySellConstant::TYPE_W);
    }

    public function es_drop_hub_log () {
        return $this->hasOne(EasysellProductLog::class, "offer_id", "offer_id")->where("w_type", EasySellConstant::TYPE_DROPHUB);
    }

    public function oc_public_log () {
        return $this->hasOne(OnchannelProductLog::class, "offer_id", "offer_id")->where("send_type", OnchannelConstant::PRD_CHANNEL);
    }

    public function oc_private_log () {
        return $this->hasOne(OnchannelProductLog::class, "offer_id", "offer_id")->where("send_type", OnchannelConstant::PRD_CHANNEL_PRIVATE);
    }

    public function forbidden_prd_name()
    {
        return $this->hasOne(ProductForbiddenData::class, "offer_id", "offer_id")->where("apply_type", ForbiddenWordConstant::KEYWORD_APPLY_TITLE);
    }

    public function forbidden_notice_names()
    {
        return $this->hasMany(ProductForbiddenData::class, "offer_id", "offer_id")->where("apply_type", ForbiddenWordConstant::KEYWORD_APPLY_ATTR_NAME);
    }

    public function forbidden_notice_values()
    {
        return $this->hasMany(ProductForbiddenData::class, "offer_id", "offer_id")->where("apply_type", ForbiddenWordConstant::KEYWORD_APPLY_ATTR_VALUE);
    }
}
