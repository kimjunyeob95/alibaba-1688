<?php

namespace App\Models;

use App\Constants\GenuioConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GenuioAiData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'genuio_ai_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
