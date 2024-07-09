<?php

namespace App\Packages;

use App\Constants\ExchangeRateConstant;
use App\Constants\ExchangeRateErrorMessageConstant;
use App\Models\ExchangeRateHistory;
use Carbon\Carbon;
use Exception;

class ExchangeRate
{
    private array $returnMsg;
    private array $headers;
    private string $endPoint;
    private string $authKey;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
        $this->headers   = [
            "content-type: application/json"
        ];
        $this->endPoint = env('EXCHANGE_RATE_URL','https://ecos.bok.or.kr/api/StatisticSearch');
        $this->authKey  = env('EXCHANGE_RATE_KEY','1317QLDZZ3M8F37CGXL0');
    }

    public function getExchangeRate(): array
    {
        $rsMsg = $this->returnMsg;

        try{
            $searchDate   = date("Ymd", strtotime("-1 day"));
            $currencyUnit = ExchangeRateConstant::ITEM_CODE_CNH;

            /** 1일 1회 기록, 기록여부 체크 */
            $exrObj = ExchangeRateHistory::where([
                "date"          => Carbon::parse($searchDate)->format("Y-m-d"),
                "currency_unit" => ExchangeRateConstant::CURRENCY_UNIT[$currencyUnit],
            ]);
            if($exrObj->exists()){
                throw new Exception(ExchangeRateErrorMessageConstant::getFitErrorMessage("ALEADY_EXCHANGEOBJ"));
            }
            printQuery( $exrObj );

            $apiParams = [
                "searchDate"   => $searchDate,
                "currencyUnit" => $currencyUnit
            ];
            $cnhResult = $this->_callExchangeRate($apiParams);

            if($cnhResult["isSuccess"] === true){
                ExchangeRateHistory::create([
                    "date"          => Carbon::parse($searchDate)->format("Y-m-d"),
                    "exchange_rate" => $cnhResult["data"]["deal_bas_r"],
                    "currency_unit" => $cnhResult["data"]["cur_unit"]
                ]);

                $rsMsg = helpers_success_message();
            }else{
                throw new Exception($cnhResult["msg"]);
            }
        }catch(Exception $e){
            $rsMsg = helpers_fail_message($e->getMessage());
        }

        return $rsMsg;
    }

    private function _callExchangeRate(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try{
            $searchDate   = $params["searchDate"];
            $currencyUnit = $params["currencyUnit"];

            $apiParams = [
                "authkey"      => $this->authKey,
                "responseType" => ExchangeRateConstant::REQUEST_TYPE_JSON,
                "langType"     => ExchangeRateConstant::LANG_TYPE_KR,
                "startCnt"     => 1,
                "endCnt"       => 1,
                "statCode"     => ExchangeRateConstant::STAT_CODE,
                "cycle"        => ExchangeRateConstant::CYCLE_DAY,
                "startDate"    => $searchDate,
                "endDate"      => $searchDate,
                "data"         => $currencyUnit,
            ];

            $queryString = implode("/", $apiParams);

            $endPoint = $this->endPoint ."/". $queryString;
            $rsData = helpers_curl("GET", $endPoint, $this->headers);

            $res = [];
            if($rsData === false){
                throw new Exception(ExchangeRateErrorMessageConstant::getFitErrorMessage("RESPONSE"));
            }else{
                if(isset($rsData["StatisticSearch"]["row"])){
                    foreach($rsData["StatisticSearch"]["row"] as $data){
                        $res = [
                            "cur_unit"   => ExchangeRateConstant::CURRENCY_UNIT[$currencyUnit],
                            "cur_nm"     => $data['ITEM_NAME1'],
                            "deal_bas_r" => $data['DATA_VALUE'],
                        ];
                        break;
                    }
                }

                if( empty($res) ){
                    throw new Exception(ExchangeRateErrorMessageConstant::getFitErrorMessage("RESPONSE"));
                }

                $returnMsg = helpers_success_message($res);
            }
        }catch(Exception $e){
            debug_log(json_encode($rsData, JSON_UNESCAPED_UNICODE), "getExchangeRate/response", "response");
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
