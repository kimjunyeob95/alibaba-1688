<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\ExchangeRateErrorMessageConstant;
use App\Constants\HttpConstant;
use App\Http\Controllers\Controller;
use App\Services\ExchangeRateService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    private Request $request;
    private ExchangeRateService $exchangeRateService;

    function __construct(Request $request, ExchangeRateService $exchangeRateService)
    {
        $this->request             = $request;
        $this->exchangeRateService = $exchangeRateService;
    }

    public function getExchangeRate(string $date): JsonResponse
    {
        try{
            /** 날짜 형식 확인 (YYYYMMDD) */
            if (!preg_match('/^\d{8}$/', $date) || !strtotime($date)) {
                throw new Exception(ExchangeRateErrorMessageConstant::getFitErrorMessage("DATE_FORMAT"));
            }

            $result = $this->exchangeRateService->getExchangeRate($date);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        }catch (Exception $e){
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }
}
