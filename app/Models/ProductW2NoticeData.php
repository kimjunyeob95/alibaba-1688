<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductW2NoticeData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'product_w2_notice_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
