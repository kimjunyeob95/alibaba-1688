<?php

namespace App\Http\Request\Bonaera;

use Illuminate\Contracts\Validation\Rule;
use App\Constants\BonaeraErrorMessageConstant;
use App\Constants\WmsConstant;

class BonaeraDeliveryBundleTypeRule implements Rule
{
    public function passes($attribute, $value)
    {
        return in_array($value, [WmsConstant::WMS_CODE_TYPE_GR003]);
    }

    public function message()
    {
        return BonaeraErrorMessageConstant::getFitErrorMessage("TYPE");
    }
}