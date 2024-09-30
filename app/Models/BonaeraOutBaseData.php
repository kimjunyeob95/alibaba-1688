<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonaeraOutBaseData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'bonaera_out_base_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
