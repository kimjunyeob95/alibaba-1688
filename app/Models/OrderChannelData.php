<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderChannelData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table          = 'order_channel_datas';
    protected $guarded        = [];
    protected $fillable       = [];
    protected $cascadeDeletes = ['order_channel_detail_datas'];

    public function order()
    {
        return $this->hasOne(OrderBaseData::class, "order_id", "order_id");
    }

    public function details()
    {
        return $this->hasMany(OrderChannelDetailData::class, "order_channel_id", "id");
    }

    public function boneara_in_base()
    {
        return $this->hasOne(BonaeraInBaseData::class, "order_id", "order_id");
    }

    public function boneara_out_base()
    {
        return $this->hasOne(BonaeraOutBaseData::class, "order_id", "order_id");
    }
}
