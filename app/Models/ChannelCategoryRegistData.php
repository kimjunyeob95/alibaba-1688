<?php

namespace App\Models;

use App\Constants\MallConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChannelCategoryRegistData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'channel_category_regist_datas';
    protected $guarded    = [];
    protected $fillable   = [];

}
