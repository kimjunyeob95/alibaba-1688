<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderBaseData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'order_base_datas';
    protected $guarded    = [];
    protected $fillable   = [];

    public function product () {
        return $this->hasOne(ProductData::class, "offer_id", "offer_id");
    }

    public function w_options () {
        return $this->hasMany(OrderProductData::class, "order_id", "order_id");
    }

    public function logistics () {
        return $this->hasMany(OrderLogisticsData::class, "order_id", "order_id");
    }

    public function logistics_last () {
        return $this->hasOne(OrderLogisticsData::class, "order_id", "order_id")->orderBy("delivered_time", "desc");
    }

    public function channel_obj()
    {
        return $this->hasOne(OrderChannelData::class, "order_id", "order_id");
    }
}
