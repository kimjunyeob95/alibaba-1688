<?php

namespace App\Services\Order;

use App\Abstracts\OrderAbstract;
use App\Constants\Constant1688;
use Exception;

class OrderW1 extends OrderAbstract
{
    private array $returnMsg;
    private string $accessToken;

    public function __construct()
    {
        $this->returnMsg        = helpers_fail_message();
        $this->accessToken      = env("1688_ACCESS_TOKEN");
    }

    public function getWOrder(string $orderId): array
    {
        $returnMsg = $this->returnMsg;

        try {

            $endPoint = "param2/1/com.alibaba.trade/alibaba.trade.get.buyerView/";
            $payload = [
                'access_token' => $this->accessToken,
                'webSite'      => Constant1688::WEBSITE,
                'orderId'      => (int)$orderId,
            ];
            $result = curl_1688("post", $endPoint, $payload);

            if( $result["isSuccess"] === true &&
                isset($result["data"]) &&
                isset($result["data"]["result"])
            ){
                $returnMsg = helpers_success_message($result["data"]);
            } else {
                $errorMsg = $this->returnMsg["msg"];
                if( isset($result["data"]["errorMessage"]) ){
                    $errorMsg = $result["data"]["errorMessage"];
                } else if( isset($result["data"]["error_message"]) ){
                    $errorMsg = $result["data"]["error_message"];
                }

                $returnMsg = helpers_fail_message($errorMsg);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function createWOrder(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $offerId = $params["offerId"];

            $cargoParamList = [];
            foreach ($params["optionParamList"] as $option) {
                $cargoParamList[] = [
                    "offerId"  => $offerId,
                    "specId"   => $option["specId"],
                    "quantity" => $option["quantity"],
                ];
            }

            $endPoint = "param2/1/com.alibaba.trade/alibaba.trade.createCrossOrder/";
            $payload = [
                'access_token' => $this->accessToken,
                'flow'         => Constant1688::FLOW,
                'addressParam' => [
                    'addressId'    => Constant1688::ADDRESSID,
                    'fullName'     => Constant1688::FULLNAME,
                    'mobile'       => Constant1688::MOBILE,
                    'phone'        => Constant1688::PHONE,
                    'postCode'     => Constant1688::POSTCODE,
                    'cityText'     => Constant1688::CITYTEXT,
                    'provinceText' => Constant1688::PROVINCETEXT,
                    'areaText'     => Constant1688::AREATEXT,
                    'townText'     => Constant1688::TOWNTEXT,
                    'address'      => Constant1688::ADDRESS,
                    'districtCode' => Constant1688::DISTRICTCODE,
                ],
                'cargoParamList'      => $cargoParamList,
                'preSelectPayChannel' => Constant1688::PRESELECTPAYCHANNEL
            ];
            $result = curl_1688("post", $endPoint, $payload);

            if( $result["isSuccess"] === true &&
                isset($result["data"]) &&
                $result["data"]["success"] === true &&
                isset($result["data"]["result"]["orderId"])
            ){
                $returnMsg = helpers_success_message($result["data"]["result"]);
            } else {
                $errorMsg = $this->returnMsg["msg"];
                if( isset($result["data"]["message"]) ){
                    $errorMsg = $result["data"]["message"];
                } else if( isset($result["data"]["code"]) ){
                    $errorMsg = $result["data"]["code"];
                }

                $returnMsg = helpers_fail_message($errorMsg);

                debug_log(json_encode($result, JSON_UNESCAPED_UNICODE), "createWOrder", "createWOrder");
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
  
}