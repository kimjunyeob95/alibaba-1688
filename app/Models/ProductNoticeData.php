<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductNoticeData extends Model
{
    use HasFactory;

    protected $table      = 'product_notice_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
