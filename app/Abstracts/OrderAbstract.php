<?php

namespace App\Abstracts;

abstract class OrderAbstract
{
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
    * @return array
    */
    abstract function createWOrder(array $params): array;
}
