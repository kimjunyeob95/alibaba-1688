<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductExtendData extends Model
{
    use HasFactory;

    protected $table      = 'product_extend_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
