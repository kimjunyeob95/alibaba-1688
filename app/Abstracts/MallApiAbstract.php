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
     * @func orderInfo
     * @description '주문 조회'
     * @param int $orderId
     * @return array
    */
    abstract function orderInfo(int $orderId): array;

    /**
     * @func orderCreate
     * @description '주문 생성'
     * @param array $params
     * @return array
     */
    abstract function orderCreate(array $params): array;
}
