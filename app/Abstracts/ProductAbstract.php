<?php

namespace App\Abstracts;
use Illuminate\Http\UploadedFile;

abstract class ProductAbstract
{
    /**
     * @func getMallCategory
     * @description '오픈API 상품상세 endPoint 조회'
     * @param int $offerId '제품ID'
     */
    abstract function getProductData(int $offerId): array;

    /**
     * @func saveMallProductByCategotyId
     * @description '오픈API 카테고리ID별 상품수집'
     */
    abstract function saveMallProductByCategotyId(int $categoryId): void;

    /**
     * @func getKeywordQuery
     * @description '오픈API 상품 기본 조회'
     */
    abstract function getKeywordQuery(array $params): array;

    /**
     * @func getImageQuery
     * @description '오픈API 상품 이미지 조회'
     */
    abstract function getImageQuery(array $params): array;

    /**
     * @func saveKeywordQuery
     * @description '오픈API keywordQueryAPI로 상품 수집'
     */
    abstract function saveKeywordQuery(array $params): array;

    /**
     * @func createImgId
     * @description '오픈API 이미지ID 생성'
     */
    abstract function createImgId(UploadedFile $file): array;

    /**
     * @func saveImageQuery
     * @description '오픈API imageQueryAPI로 상품 수집'
     */
    abstract function saveImageQuery(array $params): array;
}
