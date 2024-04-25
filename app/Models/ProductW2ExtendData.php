<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductW2ExtendData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'product_w2_extend_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
