<?php

namespace App\Abstracts;

use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class OrderAbstract
{
    protected array $returnMsg;
    protected string $accessToken;

    public function __construct()
    {
        $this->returnMsg   = helpers_fail_message();
        $this->accessToken = env("1688_ACCESS_TOKEN");
    }

    /**
    * @func getWOrder
    * @description 'W 주문 조회'
    * @param string $orderId
    * @return array
    */
    abstract function getWOrder(string $orderId): array;

    /**
    * @func createWOrder
    * @description 'W 주문 생성'
    * @param array $params
    * @param int $totalQuantity
    * @return array
    */
    abstract function createWOrder(array $params, int $totalQuantity): array;

    /**
    * @func orderList
    * @description '주문 리스트'
    * @param array $params
    * @return array
    */
    public function getPrdList(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $page     = $params["page"];
            $pageSize = $params["pageSize"];

            $endPoint = "param2/1/com.alibaba.trade/alibaba.trade.getBuyerOrderList/";
            $payload = [
                'access_token' => $this->accessToken,
                'page'         => (int)$page,
                'pageSize'     => (int)$pageSize,
            ];
            $curlResult = curl_1688("post", $endPoint, $payload);
            
            $result = [];
            if( isset($curlResult["data"]["result"]) && !empty($curlResult["data"]["result"]) ){
                $apiData      = $curlResult["data"];
                $totalRecords = $apiData["totalRecord"];

                $paginator    = new LengthAwarePaginator(
                    collect($apiData["result"])->forPage($page, $pageSize), // 현재 페이지의 아이템들
                    $totalRecords, // 총 아이템 수
                    $pageSize, // 페이지 당 아이템 수
                    $page, // 현재 페이지
                    ['path' => LengthAwarePaginator::resolveCurrentPath()] // 현재 URL 경로
                );
            }

            $returnMsg = helpers_success_message($paginator);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
