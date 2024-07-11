<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderTradeData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'order_trade_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
