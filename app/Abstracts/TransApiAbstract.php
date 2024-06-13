<?php

namespace App\Abstracts;

use App\Traits\Genuio\QueueTrait;
use App\Traits\Genuio\WebTrait;

abstract class TransApiAbstract
{
    use WebTrait, QueueTrait;

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
     * @func createTransProductImgAgain
     * @description '추가 이미지 번역 통신'
     * @param array $product1688ImageDtoList
     * @param int $offerId
     */
    abstract function createTransProductImgAgain(array $product1688ImageDtoList, int $offerId): array;

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
     * @func channelImgTransRequest
     * @description '채널별 번역 큐등록'
     * @param string $channel
     * @param array $params
     * @return array
     */
    abstract function channelImgTransRequest(string $channel, array $params): array;

    /**
     * @func channelImgTrans
     * @description '번역 된 이미지 처리'
     * @param string $channel
     * @param array $params
     * @return array
     */
    abstract function channelImgTrans(string $channel, array $params): array;

    /**
     * @func imgUpload
     * @description '이미지 S3 upload'
     * @param string $channel
     * @param array $params
     * @return array
    */
    abstract function imgUpload(string $channel, array $params): array;
}
