<?php

namespace App\Packages;

use App\Constants\BonaeraConstant;
use Exception;


class Bonaera
{
    private array $returnMsg;
    private string $domain;
    private array $header;
    private string $userId;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
        $this->domain    = "https://bonaera.com";
        $this->header    = [
            'userKey: ' . env("BONAERA_TOKEN" , "3dI7uzN1dERvCBM1wt9wp1CglC7hcBB0jFkLZAFjZDC7SP56TIfwcJhfpTbLCIjg"),
            'Content-Type: application/json'
        ];
        $this->userId = BonaeraConstant::USER_ID;

    }

    /** 재고현황 조회 */
    public function getStockList(string $stockCode, string $itCode = null): array
    {
        $returnMsg = $this->returnMsg;
        
        $endPoint = $this->domain . '/elpisapi/stockList_api.php';
        $payload  = [
            "userId" => $this->userId,
            "stCode" => $stockCode,
        ];

        if( $itCode != null ){
            $payload["itCode"] = $itCode;
        }

        try {
            $result = helpers_curl("GET", $endPoint, $this->header, $payload);
            if( !isset($result["message"]) || $result["message"] != "success" || !isset($result["data"]["appList"][0]) ) {
                throw new Exception("stockList_api 통신");
            }

            $returnMsg = helpers_success_message($result["data"]["appList"][0]);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

}
