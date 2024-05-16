<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryMapping extends Model
{
    use HasFactory;

    protected $table      = 'category_mappings';
    protected $guarded    = [];
    protected $fillable   = [];

    public function categoryTree () {
        return $this->hasOne(CategoryTree::class, "category_id", "category_id");
    }

    public function w_cate_name()
    {
        return $this->hasOne(WCategory::class, "mapping_code", "mapping_code");
    }

    public function oc_category()
    {
        return $this->hasOne(OnchCategoryExcelDataCopy2::class, "codenum", "mapping_code");
    }
}
