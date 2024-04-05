<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EasysellProductLog extends Model
{
    use HasFactory;

    protected $table      = 'easysell_product_logs';

    protected $guarded    = [];
    protected $fillable   = [];
}
