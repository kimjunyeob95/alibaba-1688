<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EasysellProductLog extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'easysell_product_logs';
    protected $guarded    = [];
    protected $fillable   = [];

    public function detail_log () {
        return $this->hasMany(EasysellProductDetailLog::class, "log_id", "id");
    }

    public function last_log () {
        return $this->hasOne(EasysellProductDetailLog::class, "log_id", "id")->orderBy("created_at", "desc");
    }
}