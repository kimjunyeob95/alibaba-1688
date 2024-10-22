<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonaeraOutDeliveryExtraData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'bonaera_out_delivery_extra_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
