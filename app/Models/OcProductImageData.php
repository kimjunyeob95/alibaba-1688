<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OcProductImageData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $connection = "oc_2013";

    protected $table      = 'oc_product_image_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
