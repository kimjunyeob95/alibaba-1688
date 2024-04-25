<?php

namespace App\Models;

use App\Constants\GenuioConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductW2ImageData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'product_w2_image_datas';
    protected $guarded    = [];
    protected $fillable   = [];

    public function ai_origin_img () {
        return $this->hasOne(GenuioImageData::class, "img_id", "id")->where("is_origin", GenuioConstant::IS_ORIGIN_Y);
    }

    public function ai_imgs () {
        return $this->hasMany(GenuioImageData::class, "img_id", "id")->where("is_origin", GenuioConstant::IS_ORIGIN_N)->orderBy("created_at", "desc");
    }

    public function ai_all_imgs () {
        return $this->hasMany(GenuioImageData::class, "img_id", "id")->orderBy("created_at", "desc");
    }
}
