<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerhubCategory extends Model
{
    use HasFactory;

    protected $connection = "oc_2013";

    protected $table = 'sellerhub_category';
    protected $primaryKey = 'num';

    const CREATED_AT = null;
    const UPDATED_AT = null;
}
