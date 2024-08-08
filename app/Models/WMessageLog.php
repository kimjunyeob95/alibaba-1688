<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WMessageLog extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'w_message_logs';
    protected $guarded    = [];
    protected $fillable   = [];

    public function order_base_obj () {
        return $this->hasOne(OrderBaseData::class, "order_id", "order_id");
    }
}
