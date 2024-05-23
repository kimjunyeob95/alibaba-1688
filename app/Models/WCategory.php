<?php

namespace App\Models;

use App\Constants\ProductConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WCategory extends Model
{
    use HasFactory;

    protected $table      = 'w_categories';
    protected $guarded    = [];
    protected $fillable   = [];

    public function categoryTree () {
        return $this->hasOne(CategoryTree::class, "category_id", "category_id");
    }

    public function categoryMapping () {
        return $this->hasOne(CategoryMapping::class, "mapping_code", "mapping_code")->where("mapping_channel", ProductConstant::MAPPING_WAPP);
    }

    public function es_category()
    {
        return $this->hasOne(SellerhubCategory::class, "sellerhub_cate", "es_mapping_code");
    }
}
