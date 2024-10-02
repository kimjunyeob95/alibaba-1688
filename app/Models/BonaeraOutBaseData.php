<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonaeraOutBaseData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'bonaera_out_base_datas';
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

    public function out_options () {
        return $this->hasMany(BonaeraOutProductData::class, "sh_no", "sh_no");
    }

    public function out_delivery () {
        return $this->hasOne(BonaeraOutDeliveryData::class, "group_no", "group_no");
    }

    public function out_weight () {
        return $this->hasOne(BonaeraOutWeightData::class, "group_no", "group_no");
    }
}
