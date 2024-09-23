<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaobaoTokenData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'taobao_token_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
