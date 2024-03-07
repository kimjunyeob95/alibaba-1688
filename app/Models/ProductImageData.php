<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImageData extends Model
{
    use HasFactory;

    protected $table      = 'product_image_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
