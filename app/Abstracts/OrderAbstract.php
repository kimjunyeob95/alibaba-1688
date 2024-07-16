<?php

namespace App\Abstracts;

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
    abstract function orderList(array $params): array;

    /**
    * @func orderUpdate
    * @description 'W -> WApp 주문 업데이트'
    * @param array $orderIds
    * @return array
    */
    abstract function orderUpdate(array $orderIds): array;

    /**
    * @func orderPayLinkCreate
    * @description 'WApp 주문 결제 링크 생성'
    * @param array $params
    * @return array
    */
    abstract function orderPayLinkCreate(array $orderIds): array;
}
