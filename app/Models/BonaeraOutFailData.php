<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonaeraOutFailData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'bonaera_out_fail_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
