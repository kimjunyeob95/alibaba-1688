<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryWeightData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'category_weight_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
