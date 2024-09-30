<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonaeraOutProductData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'bonaera_out_product_datas';
    protected $guarded    = [];
    protected $fillable   = [];

    public function w_option () {
        return $this->hasOne(ProductOptionData::class, "id", "option_id");
    }
}
