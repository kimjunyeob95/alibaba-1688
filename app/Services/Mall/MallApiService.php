<?php

namespace App\Services\Mall;

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

    /****************************************** 토큰 start **********************************************/

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

    /****************************************** 토큰 end **********************************************/

    /****************************************** 상품 start **********************************************/

    /**
     * @func productRegist
     * @description '상품등록'
     * @param array $offerIds
     * @param array $params
     * @return array
    */
    public function productRegist(array $offerIds, array $params = []): array
    {
        return $this->mallApiAbstract->productRegist($offerIds, $params);
    }

    /**
     * @func productLog
     * @description '상품전송 로그'
     * @param int $logId
     * @return array
    */
    public function productLog(int $logId): array
    {
        return $this->mallApiAbstract->productLog($logId);
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

    /**
     * @func productRegistLog
     * @description '모든 채널 상품 등록 전송 로그'
     * @param int $offerId
     * @return array
    */
    public function productRegistLog(int $offerId): array
    {
        return $this->mallApiAbstract->productRegistLog($offerId);
    }

    /**
     * @func productWappRegist
     * @description 'WApp 상품 생성 후 채널 전송'
     * @param int $offerId
     * @param array $params
     * @return array
    */
    public function productWappRegist(int $offerId, array $params): array
    {
        return $this->mallApiAbstract->productWappRegist($offerId, $params);
    }

    /****************************************** 상품 end **********************************************/

    /****************************************** 주문 start **********************************************/

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
     * @func orderPreview
     * @description '주문 미리보기'
     * @param array $params
     * @return array
    */
    public function orderPreview(array $params): array
    {
        return $this->mallApiAbstract->orderPreview($params);
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

    /****************************************** 주문 end **********************************************/

    /****************************************** 이미지 start **********************************************/

    /**
     * @func imgTransRequest
     * @description '이미지 번역 요청'
     * @param array $params
     * @return array
    */
    public function imgTransRequest(array $params): array
    {
        return $this->mallApiAbstract->imgTransRequest($params);
    }

    /**
     * @func imgTrans
     * @description '번역 된 이미지 처리'
     * @param array $params
     * @return array
    */
    public function imgTrans(array $params): array
    {
        $result = $this->mallApiAbstract->imgTrans($params);

        $this->mallApiAbstract->imgCallBack($result);

        return $result;
    }

    /**
     * @func imgUpload
     * @description '이미지 S3 upload'
     * @param array $params
     * @return array
    */
    public function imgUpload(array $params): array
    {
        return $this->mallApiAbstract->imgUpload($params);
    }

    /****************************************** 이미지 end **********************************************/
}