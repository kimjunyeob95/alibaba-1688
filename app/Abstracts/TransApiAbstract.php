<?php

namespace App\Abstracts;

abstract class TransApiAbstract
{
    protected array $returnMsg;
    protected string $user_company;

    public function __construct(string $user_company)
    {
        $this->returnMsg    = helpers_fail_message();
        $this->user_company = $user_company;
    }

    /**
     * @func createTransProductImg
     * @description '이미지 번역 통신'
     * @param array $product1688ImageDtoList
     * @param int $offerId
     */
    abstract function createTransProductImg(array $product1688ImageDtoList, int $offerId): array;

    /**
     * @func translateImage
     * @description '이미지 번역'
     * @param string $imgPath
     */
    abstract function translateImage(string $imgPath): array;
    
    /**
     * @func tokenCreate
     * @description '토큰 생성'
     * @param array $params
     */
    abstract function tokenCreate(array $params): array;

    /**
     * @func imgTrans
     * @description '번역된 이미지 처리'
     * @param array $params
     */
    abstract function imgTrans(array $params): array;

    /**
     * @func imgTransRequest
     * @description '상품 이미지 번역 요청'
     * @param array $offerIds
     */
    abstract function imgTransRequest(array $offerIds): array;

    /**
     * @func removeQueue
     * @description '큐 삭제'
     * @param int $queueId
     * @return array
     */
    abstract function removeQueue(int $queueId): array;
}
