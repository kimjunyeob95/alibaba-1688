<?php

namespace App\Abstracts;

abstract class MallApiAbstract
{
    protected array $returnMsg;

    public function __construct()
    {
        $this->returnMsg    = helpers_fail_message();
    }

    /**
     * @func registProduct
     * @description '상품등록'
     */
    abstract function registProduct(): array;

    /**
     * @func modiProduct
     * @description '상품수정'
     */
    abstract function modiProduct(): array;

    /**
     * @func getOrders
     * @description '주문수집'
     */
    abstract function getOrders(): array;
}
