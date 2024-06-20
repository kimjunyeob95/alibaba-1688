<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCollectTypeData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'product_collect_type_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
