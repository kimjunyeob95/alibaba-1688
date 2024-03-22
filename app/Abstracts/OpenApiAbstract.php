<?php

namespace App\Abstracts;

abstract class OpenApiAbstract
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
     */
    abstract function tokenCreate(array $params): array;

    /**
     * @func imgTrans
     * @description '번역된 이미지 처리'
     */
    abstract function imgTrans(array $params): array;
}
