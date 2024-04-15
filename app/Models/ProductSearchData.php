<?php

namespace App\Models;

use App\Constants\ProductConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSearchData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'product_search_datas';
    protected $guarded    = [];
    protected $fillable   = [];

    public function details()
    {
        return $this->hasMany(ProductSearchDetailData::class, "search_id", "id")->orderBy("sold_out", "desc");
    }

    public function details_y_cnt()
    {
        return $this->details()->where("is_search", ProductConstant::IS_SEARCH_Y)->count();
    }

    public function details_n_cnt()
    {
        return $this->details()->where("is_search", ProductConstant::IS_SEARCH_N)->count();
    }
}
