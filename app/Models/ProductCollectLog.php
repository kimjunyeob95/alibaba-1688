<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCollectLog extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'product_collect_logs';
    protected $guarded    = [];
    protected $fillable   = [];

    public function details () {
        return $this->hasMany(ProductCollectDetailLog::class, "log_id", "id")->oldest("updated_at");
    }
}
