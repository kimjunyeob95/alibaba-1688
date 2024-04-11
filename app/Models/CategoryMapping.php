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
}
