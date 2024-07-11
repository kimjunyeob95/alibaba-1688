<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderLogisticsData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'order_logistics_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
