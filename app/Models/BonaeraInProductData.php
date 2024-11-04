<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonaeraInProductData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'bonaera_in_product_datas';
    protected $guarded    = [];
    protected $fillable   = [];

    public function imgs () {
        return $this->hasMany(BonaeraInProductImgData::class, "product_id", "id");
    }

    public function w_option () {
        return $this->hasOne(ProductOptionData::class, "id", "option_id");
    }
}
