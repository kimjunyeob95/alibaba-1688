<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnchannelProductLog extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'onchannel_product_logs';
    protected $guarded    = [];
    protected $fillable   = [];
}
