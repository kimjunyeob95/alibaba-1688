<?php

namespace App\Models;

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
}
