<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductData extends Model
{
    use HasFactory;

    protected $table      = 'product_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
