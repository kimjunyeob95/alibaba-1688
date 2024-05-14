<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForbiddenNoticeWordData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'forbidden_notice_word_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
