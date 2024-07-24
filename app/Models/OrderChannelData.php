<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderChannelData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'order_channel_datas';
    protected $guarded    = [];
    protected $fillable   = [];

    public function details()
    {
        return $this->hasMany(OrderChannelDetailData::class, "order_channel_id", "id");
    }
}
