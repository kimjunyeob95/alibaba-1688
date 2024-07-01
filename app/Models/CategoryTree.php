<?php

namespace App\Models;

use App\Constants\MallConstant;
use App\Constants\ProductConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryTree extends Model
{
    use HasFactory;

    protected $table      = 'category_trees';
    protected $guarded    = [];
    protected $fillable   = [];

    public function category () {
        return $this->hasOne(Category::class, "category_id", "category_id");
    }

    public function w_category () {
        return $this->hasOne(CategoryMapping::class, "category_id", "category_id")->where("mapping_channel", ProductConstant::MAPPING_WAPP);
    }

    public function weight_category () {
        return $this->hasOne(CategoryWeightData::class, "category_id", "category_id");
    }

    public function cate_oc_public()
    {
        return $this->hasOne(ChannelCategoryRegistData::class, "category_id", "category_id")->where("channel", MallConstant::MALL_ONCHANNEL)
        ->where("send_type", MallConstant::OC_PUBLIC)->where("is_regist", MallConstant::REGIST_Y);
    }

    public function cate_oc_private()
    {
        return $this->hasOne(ChannelCategoryRegistData::class, "category_id", "category_id")->where("channel", MallConstant::MALL_ONCHANNEL)
        ->where("send_type", MallConstant::OC_PRIVATE)->where("is_regist", MallConstant::REGIST_Y);
    }

    public function cate_es_w()
    {
        return $this->hasOne(ChannelCategoryRegistData::class, "category_id", "category_id")->where("channel", MallConstant::MALL_EASYSELL)
        ->where("send_type", MallConstant::EASYSELL_W)->where("is_regist", MallConstant::REGIST_Y);
    }

    public function cate_es_drophub()
    {
        return $this->hasOne(ChannelCategoryRegistData::class, "category_id", "category_id")->where("channel", MallConstant::MALL_EASYSELL)
        ->where("send_type", MallConstant::EASYSELL_DROPHUB)->where("is_regist", MallConstant::REGIST_Y);
    }
}
