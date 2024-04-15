<?php

namespace App\Models;

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
        return $this->hasOne(WCategory::class, "category_id", "category_id");
    }
}
