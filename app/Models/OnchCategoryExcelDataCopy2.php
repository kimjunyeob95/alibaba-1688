<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnchCategoryExcelDataCopy2 extends Model
{
    use HasFactory;

    protected $connection = "oc_2013";

    protected $table = 'onch_category_excel_data_copy2';
    protected $primaryKey = 'num';

    const CREATED_AT = 'wdate';
    const UPDATED_AT = null;
}
