<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GenuioImageData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'genuio_image_datas';
    protected $guarded    = [];
    protected $fillable   = [];
}
