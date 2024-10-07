<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonaeraInFailData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'bonaera_in_fail_datas';
    protected $guarded    = [];
    protected $fillable   = [];

    public function order () {
        return $this->hasOne(OrderBaseData::class, "order_id", "order_id");
    }

    public function w_options () {
        return $this->hasMany(OrderProductData::class, "order_id", "order_id");
    }

    public function logistics_last () {
        return $this->hasOne(OrderLogisticsData::class, "order_id", "order_id")->orderBy("delivered_time", "desc");
    }
}
