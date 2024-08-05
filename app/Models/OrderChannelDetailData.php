<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderChannelDetailData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'order_channel_detail_datas';
    protected $guarded    = [];
    protected $fillable   = [];

    public function order_channel()
    {
        return $this->belongsTo(OrderChannelData::class, 'id', 'order_channel_id');
    }
}
