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
        $this->endPoint = env('EXCHANGE_RATE_URL','https://www.koreaexim.go.kr/site/program/financial/exchangeJSON');
        $this->authKey  = env('EXCHANGE_RATE_KEY','OjId9ekbQbVxVxYpsqacQCVnk4emDYCp');
    }

    public function getExchangeRate(): array
    {
        $rsMsg = $this->returnMsg;

        try{
            $searchDate   = date("Ymd");
            $currencyUnit = ExchangeRateConstant::CURRENCY_UNIT;

            /** 기존에 호출된 날짜가 있으면 통신이 에러가 발생하므로 기록여부 체크 */
            $exrObj = ExchangeRateHistory::where([
                "date"          => Carbon::parse($searchDate)->format("Y-m-d"),
                "currency_unit" => $currencyUnit,
            ]);
            if($exrObj->exists()){
                throw new Exception(ExchangeRateErrorMessageConstant::getFitErrorMessage("ALEADY_EXCHANGEOBJ"));
            }

            $apiParams = [
                "searchDate"   => $searchDate,
                "currencyUnit" => $currencyUnit
            ];
            $cnhResult = $this->_callExchangeRate($apiParams);

            if($cnhResult["isSuccess"] === true){
                ExchangeRateHistory::create([
                    "date"          => Carbon::parse($searchDate)->format("Y-m-d"),
                    "exchange_rate" => $cnhResult["data"]["deal_bas_r"],
                    "currency_unit" => $currencyUnit
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
                "authkey"    => $this->authKey,
                "searchdate" => $searchDate,
                "data"       => ExchangeRateConstant::AP01,
            ];
            $rsData = helpers_curl("GET", $this->endPoint, $this->headers, $apiParams);

            $res = [];
            if(is_array($rsData) === true){
                foreach($rsData as $data){
                    if($data['cur_unit'] == $currencyUnit && $data["result"] == 1){
                        $res = [
                            "cur_unit"   => $data['cur_unit'],
                            "cur_nm"     => $data['cur_nm'],
                            "deal_bas_r" => $data['deal_bas_r'],
                        ];
                        break;
                    }
                }

                if( empty($res) ){
                    throw new Exception(ExchangeRateErrorMessageConstant::getFitErrorMessage("RESPONSE"));
                }

                $returnMsg = helpers_success_message($res);
            }else{
                throw new Exception(ExchangeRateErrorMessageConstant::getFitErrorMessage("RESPONSE"));
            }
        }catch(Exception $e){
            debug_log(json_encode($rsData, JSON_UNESCAPED_UNICODE), "getExchangeRate/response", "response");
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
