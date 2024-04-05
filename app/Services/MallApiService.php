<?php

namespace App\Services;

use App\Abstracts\MallApiAbstract;

class MallApiService
{
    protected array $returnMsg;
    private MallApiAbstract $mallApiAbstract;

    public function __construct(MallApiAbstract $mallApiAbstract)
    {
        $this->mallApiAbstract = $mallApiAbstract;
        $this->returnMsg       = helpers_fail_message();
    }

    public function productRegist(): array
    {
        return $this->mallApiAbstract->productRegist();
    }

    /**
     * @func orderInfo
     * @description '주문 조회'
     * @param int $orderId
     * @return array
    */
    public function orderInfo(int $orderId): array
    {
        return $this->mallApiAbstract->orderInfo($orderId);
    }

    /**
     * @func orderCreate
     * @description '주문 생성'
     * @param array $params
     * @return array
    */
    public function orderCreate(array $params): array
    {
        return $this->mallApiAbstract->orderCreate($params);
    }
}