<?php

namespace App\Services;

use App\Constants\ExchangeRateErrorMessageConstant;
use App\Models\ExchangeRateHistory;
use Carbon\Carbon;
use Exception;

class ExchangeRateService
{
    protected array $returnMsg;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    /**
     * @func getExchangeRate
     * @description '환율 조회'
     * @param string $date
     * @return array
     */
    public function getExchangeRate(string $date): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $searchDate  = Carbon::parse($date)->format("Y-m-d");
            $exchangeObj = ExchangeRateHistory::where("date", $searchDate)->first();
            if( $exchangeObj === null ){
                throw new Exception(ExchangeRateErrorMessageConstant::getNotHaveErrorMessage("EXCHANGEOBJ"));   
            }

            $result = [
                "date"          => $exchangeObj->date,
                "currency_unit" => $exchangeObj->currency_unit,
                "exchange_rate" => $exchangeObj->exchange_rate,
            ];

            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}