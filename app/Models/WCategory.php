<?php

namespace App\Models;

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
}
