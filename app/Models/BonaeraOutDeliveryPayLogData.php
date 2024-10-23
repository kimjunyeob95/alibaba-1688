<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonaeraOutDeliveryPayLogData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'bonaera_out_delivery_pay_log_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
