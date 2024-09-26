<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HsCodeData extends Model
{
    use HasFactory;

    protected $table      = 'hs_code_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
