<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductW2OptionData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'product_w2_option_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
