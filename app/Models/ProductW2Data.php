<?php

namespace App\Models;

use App\Constants\ImageConstant;
use App\Constants\ProductConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductW2Data extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'product_w2_datas';
    protected $guarded    = [];
    protected $fillable   = [];

    public function main_img () {
        return $this->hasOne(ProductW2ImageData::class, "offer_id", "offer_id")->where("img_type", ImageConstant::IMAGE_TYPE_MAIN);
    }

    public function sub_imgs () {
        return $this->hasMany(ProductW2ImageData::class, "offer_id", "offer_id")->where("img_type", ImageConstant::IMAGE_TYPE_SUB);
    }

    public function desc_imgs () {
        return $this->hasMany(ProductW2ImageData::class, "offer_id", "offer_id")->where("img_type", ImageConstant::IMAGE_TYPE_DESC);
    }

    public function images () {
        return $this->hasMany(ProductW2ImageData::class, "offer_id", "offer_id")->orderBy('img_type', 'asc');
    }

    public function options () {
        return $this->hasMany(ProductW2OptionData::class, "offer_id", "offer_id")->oldest("id");
    }

    public function extends () {
        return $this->hasOne(ProductW2ExtendData::class, "offer_id", "offer_id")->oldest("id");
    }

    public function notices () {
        return $this->hasMany(ProductW2NoticeData::class, "offer_id", "offer_id")->oldest("id");
    }

    public function category () {
        return $this->hasOne(CategoryTree::class, "category_id", "category_id");
    }

    public function w_mapping () {
        return $this->hasOne(CategoryMapping::class, "category_id", "category_id")->where("mapping_channel", ProductConstant::MAPPING_WAPP);
    }

    public function oc_mapping () {
        return $this->hasOne(CategoryMapping::class, "category_id", "category_id")->where("mapping_channel", ProductConstant::MAPPING_OC_CHANNEL);
    }

    public function es_mapping () {
        return $this->hasOne(CategoryMapping::class, "category_id", "category_id")->where("mapping_channel", ProductConstant::MAPPING_ES_CHANNEL);
    }

    public function es_fgn_mapping () {
        return $this->hasOne(CategoryMapping::class, "category_id", "category_id")->where("mapping_channel", ProductConstant::MAPPING_ES_FGN_CHANNEL);
    }

    public function easysell () {
        return $this->hasOne(EasysellProductLog::class, "offer_id", "offer_id");
    }
}
