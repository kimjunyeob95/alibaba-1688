<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRateHistory extends Model
{
    use HasFactory;

    const UPDATED_AT = null;
    const DELETED_AT = null;

    protected $table    = 'exchange_rate_histories';
    protected $guarded  = [];
    protected $fillable = [];
}
