<?php

namespace App\Http\Request\Bonaera;

use Illuminate\Contracts\Validation\Rule;
use App\Constants\BonaeraErrorMessageConstant;
use App\Constants\WmsConstant;

class BonaeraInTypeRule implements Rule
{
    public function passes($attribute, $value)
    {
        return in_array($value, [WmsConstant::WMS_CODE_TYPE_IT001, WmsConstant::WMS_CODE_TYPE_IT002]);
    }

    public function message()
    {
        return BonaeraErrorMessageConstant::getFitErrorMessage("TYPE");
    }
}