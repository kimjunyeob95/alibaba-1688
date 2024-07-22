<?php

namespace App\Models;

use App\Constants\GenuioConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductChannelPriceData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'product_channel_price_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
