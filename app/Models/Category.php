<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table      = 'categories';
    protected $guarded    = [];
    protected $fillable   = [];
    
    // 자식 카테고리들을 가져오기 위한 일대다 관계 설정
    public function childCategories()
    {
        return $this->hasMany(Category::class, 'parent_cate_id', 'category_id');
    }
}
