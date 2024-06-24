<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnchProductData extends Model
{
    use HasFactory;

    protected $connection = "oc_2013";

    protected $table      = 'onch_product_data';
    protected $primaryKey = 'num';
    
    const CREATED_AT = 'wdate';
    const UPDATED_AT = null;
}
