<?php

namespace App\Abstracts;

abstract class MallApiAbstract
{
    protected array $returnMsg;
    protected mixed $mallApiAbstract;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    /**
     * @func productRegist
     * @description '상품등록'
     */
    abstract function productRegist(): array;

    /**
     * @func getOrders
     * @description '주문 생성'
     * @param int $params
     * @return array
     */
    abstract function orderCreate(array $params): array;
}
