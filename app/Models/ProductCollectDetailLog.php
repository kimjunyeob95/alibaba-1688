<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCollectDetailLog extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'product_collect_detail_logs';
    protected $guarded    = [];
    protected $fillable   = [];
}
