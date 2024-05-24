<?php

namespace App\Models;

use App\Constants\ExceptConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductNoticeData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'product_notice_datas';
    protected $guarded    = [];
    protected $fillable   = [];

    public function except_data () {
        return $this->hasOne(ProductExceptData::class, "attribute_id", "attribute_id")->where("except_type", ExceptConstant::EXCEPT_NOTICE);
    }
}
