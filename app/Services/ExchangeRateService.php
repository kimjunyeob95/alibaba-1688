<?php

namespace App\Services;

use App\Constants\HttpConstant;
use App\Constants\WConstant;
use App\Models\ExchangeRateHistory;
use Carbon\Carbon;
use Exception;
use Psr\Log\LogLevel;

class ExchangeRateService
{
    private $headers;

    public function __construct()
    {
        $this->headers   = [
            "content-type: application/json"
        ];
    }

    public function getExchangeRate():array
    {
        $rsMsg = helpers_default_message();

        try{
            $searchDate   = date("Ymd");
            $currencyUnit = WConstant::CURRENCY_UNIT;

            //이미 기록됐는지 확인
            $selObj = ExchangeRateHistory::where("date", Carbon::parse($searchDate)->format("Y-m-d"))->where("currency_unit", $currencyUnit);
            if($selObj->exists()){
                throw new Exception("해당 일자의 환율 기록이 이미 존재합니다");
            }

            $apiParams = [
                "searchDate"   => $searchDate,
                "currencyUnit" => $currencyUnit
            ];
            $exchangeRateList = $this->_callExchangeRate($apiParams);

            if($exchangeRateList["code"] == HttpConstant::OK){
                ExchangeRateHistory::create([
                    "date"          => Carbon::parse($searchDate)->format("Y-m-d"),
                    "exchange_rate" => $exchangeRateList["data"]["deal_bas_r"],
                    "currency_unit" => $currencyUnit
                ]);

                $rsMsg = helpers_success_message();
            }else{
                throw new Exception(json_encode($exchangeRateList, JSON_UNESCAPED_UNICODE));
            }
        }catch(Exception $e){
            $rsMsg = helpers_fail_message($e->getMessage());
            debug_log($e->getMessage(), "exchangeRate", "exchageRate", LogLevel::ERROR);
        }

        return $rsMsg;
    }

    private function _callExchangeRate(array $params):array
    {
        try{
            $return = [
                "code" => HttpConstant::INTERNAL_SERVER_ERROR,
                "msg"  => ""
            ];

            $searchDate   = $params["searchDate"];
            $currencyUnit = $params["currencyUnit"];

            //한국 수출입은행 api url
            $apiUrl  = env('EXCHANGE_RATE_URL','https://www.koreaexim.go.kr/site/program/financial/exchangeJSON');
            $authKey = env('EXCHANGE_RATE_KEY','OjId9ekbQbVxVxYpsqacQCVnk4emDYCp');

            $apiParams = [
                "authkey"    => $authKey,
                "searchdate" => $searchDate,
                "data"       => "AP01", //환율
            ];

            $rsData = helpers_curl("GET", $apiUrl, $this->headers, $apiParams);

            if($rsData === false){
                $return = [
                    "code" => HttpConstant::INTERNAL_SERVER_ERROR,
                    "msg"  => "조회 실패",
                ];
            }else{
                foreach($rsData as $idx => $data){
                    if(!$idx){
                        $return = [
                            "code"       => $data['result'] == 1 ? HttpConstant::OK : HttpConstant::BAD_REQUEST,
                            "result"     => $data['result'],
                            "searchDate" => $searchDate,
                            "data"       => []
                        ];
                    }

                    if($data['cur_unit'] == $currencyUnit){
                        $return["data"] = [
                            "cur_unit"   => $data['cur_unit'],
                            "cur_nm"     => $data['cur_nm'],
                            "deal_bas_r" => $data['deal_bas_r'],
                        ];
                    }
                }
            }
        }catch(Exception $e){
            $return = [
                "code" => HttpConstant::BAD_REQUEST,
                "msg"  => $e->getMessage()
            ];
        }

        return $return;
    }
}