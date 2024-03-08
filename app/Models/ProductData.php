<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'product_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
