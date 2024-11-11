<?php

namespace App\Models;

use App\Constants\BonaeraConstant;
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

    public function pay_fail_log () {
        return $this->hasOne(BonaeraOutDeliveryPayLogData::class, "group_no", "group_no")->where("success", BonaeraConstant::DELIVERY_PAY_N);
    }

    public function out_extras () {
        return $this->hasMany(BonaeraOutExtraData::class, "sh_no", "sh_no");
    }

    public function out_delivery_extras () {
        return $this->hasMany(BonaeraOutDeliveryExtraData::class, "group_no", "group_no");
    }
}
