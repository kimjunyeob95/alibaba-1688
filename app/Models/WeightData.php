<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightData extends Model
{
    use HasFactory;

    protected $table      = 'weight_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
