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

    public function last_log () {
        return $this->hasOne(OnchannelProductDetailLog::class, "log_id", "id")->orderBy("created_at", "desc");
    }
}
