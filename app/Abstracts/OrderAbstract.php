<?php

namespace App\Abstracts;

abstract class OrderAbstract
{
    /**
    * @func createWOrder
    * @description 'W 주문 생성'
    * @param array $params
    * @return array
    */
    abstract function createWOrder(array $params): array;
}
