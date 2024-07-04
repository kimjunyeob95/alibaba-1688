<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\HttpConstant;
use App\Http\Controllers\Controller;
use App\Models\ExchangeRateHistory;
use Exception;
use Illuminate\Http\JsonResponse;

class ExchangeRateController extends Controller
{
    public function getExchangeRate(): JsonResponse
    {
        try{
            $lastestObj = ExchangeRateHistory::orderBy("date", "desc")->first();

            if(isset($lastestObj)){
                $result = [
                    "data" => [
                        "date"          => $lastestObj->date,
                        "currency_unit" => $lastestObj->currency_unit,
                        "exchange_rate" => $lastestObj->exchange_rate,
                    ]
                ];
                return helpers_json_response(HttpConstant::OK, $result);
            }else{
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], "환율 정보가 없습니다");
            }
        }catch (Exception $e){
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }
}
