<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOptionData extends Model
{
    use HasFactory;

    protected $table      = 'product_option_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
