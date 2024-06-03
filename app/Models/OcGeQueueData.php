<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OcGeQueueData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'oc_ge_queue_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
