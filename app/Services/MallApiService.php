<?php

namespace App\Services;

use App\Abstracts\MallApiAbstract;
use App\Constants\WConstant;

class MallApiService
{
    protected array $returnMsg;
    private MallApiAbstract $mallApiAbstract;

    public function __construct(MallApiAbstract $mallApiAbstract)
    {
        $this->mallApiAbstract = $mallApiAbstract;
        $this->returnMsg       = helpers_fail_message();
    }

    /**
     * @func tokenCreate
     * @description '토큰 생성'
     * @param array $params
     * @return array
     */
    public function tokenCreate(array $params): array
    {
        return $this->mallApiAbstract->tokenCreate($params);
    }

    /**
     * @func productRegist
     * @description '상품등록'
     * @param array $offerIds
     * @return array
    */
    public function productRegist(array $offerIds, string $type = WConstant::WAPP_W1): array
    {
        return $this->mallApiAbstract->productRegist($offerIds, $type);
    }

    /**
     * @func orderInfo
     * @description '주문 조회'
     * @param string $orderId
     * @return array
    */
    public function orderInfo(string $orderId): array
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

    /**
     * @func categoryMapping
     * @description '카테고리 매핑'
     * @return array
     */
    public function categoryMapping() :array
    {
        return $this->mallApiAbstract->categoryMapping();
    }

    /**
     * @func sendModiProduct
     * @description '수정 된 상품 전송'
     * @return void
     */
    public function sendModiProduct(): void
    {
        $this->mallApiAbstract->sendModiProduct();
    }
}