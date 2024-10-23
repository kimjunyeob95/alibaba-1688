<?php

namespace App\Models;

use App\Constants\BonaeraConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonaeraOutDeliveryData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'bonaera_out_delivery_datas';
    protected $guarded    = [];
    protected $fillable   = [];

    public function pay_fail_log () {
        return $this->hasOne(BonaeraOutDeliveryPayLogData::class, "group_no", "group_no")->where("success", BonaeraConstant::DELIVERY_PAY_N);
    }
}
