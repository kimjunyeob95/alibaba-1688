<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonaeraInProductImgData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'bonaera_in_product_img_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
