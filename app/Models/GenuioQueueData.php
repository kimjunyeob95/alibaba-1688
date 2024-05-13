<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GenuioQueueData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'genuio_queue_datas';
    protected $guarded    = [];
    protected $fillable   = [];

    public function childrenObj()
    {
        return $this->hasOne(GenuioQueueData::class, 'parent_id');
    }
}
