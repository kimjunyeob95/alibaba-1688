<?php

namespace App\Abstracts;

use App\Constants\LogConstant;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class ProductAbstract
{
    /**
    * @func getPrdList
    * @description '1688 수집 상품 리스트'
    * @param array $params
    * @return array
    */
    abstract function getPrdList(array $params): array;

    /**
     * @func getMallCategory
     * @description '1688API 수집 상품 디테일'
     * @param int $offerId '제품ID'
     * @return array
     */
    abstract function getPrdDetail(int $offerId): array;

    /**
     * @func getMallCategory
     * @description '1688API 상품상세 endPoint 조회'
     * @param int $offerId '제품ID'
     * @return array
     */
    abstract function getProductData(int $offerId): array;

    /**
     * @func saveMallProductByCategotyId
     * @description '1688API 카테고리ID별 상품수집'
     * @param int $categoryId '카테고리ID'
     * @return void
     */
    abstract function saveMallProductByCategotyId(int $categoryId): void;
    
    /**
     * @func saveMallProductByImageId
     * @description '1688API 이미지ID로 상품 수집'
     * @param int $string 'imageId'
     * @return void
     */
    abstract function saveMallProductByImageId(string $imageId): void;

    /**
     * @func getQueryProductDetail
     * @description '상품ID로 조회'
     * @param array $offerIds
     * @return array
     */
    abstract function getQueryProductDetail(array $offerIds): array;

    /**
     * @func getKeywordQuery
     * @description '상품 기본 정보로 조회'
     * @param array $params
     * @return array
     */
    abstract function getKeywordQuery(array $params): array;

    /**
     * @func getImageQuery
     * @description '상품 이미지로 조회'
     * @param array $params
     * @return array
     */
    abstract function getImageQuery(array $params): array;

    /**
     * @func getImageQuery
     * @description '상품상세 URL로 수집'
     * @param array $urls
     * @return array
     */
    abstract function getUrlQuery(array $urls): array;

    /**
     * @func getPrdCollectLogList
     * @description '상품 수집현황 조회'
     * @param array $params
     * @return LengthAwarePaginator
     */
    abstract function getPrdCollectLogList(array $params): LengthAwarePaginator;

    /**
     * @func saveKeywordQuery
     * @description '1688API keywordQueryAPI로 상품 수집'
     * @param array $params
     * @return array
     */
    abstract function saveKeywordQuery(array $params): array;

    /**
     * @func createImgId
     * @description '1688API 이미지ID 생성'
     * @param UploadedFile $file
     * @return array
     */
    abstract function createImgId(UploadedFile $file): array;

    /**
     * @func saveImageQuery
     * @description '1688API imageQueryAPI로 상품 수집'
     * @param array $params
     * @return array
     */
    abstract function saveImageQuery(array $params): array;

    /**
     * @func prdCollectLogDetail
     * @description '상품 수집 현황 조회'
     * @param int $logId
     * @return array
     */
    abstract function prdCollectLogDetail(int $logId): array;

    /**
     * @func collectProduct
     * @description '1688API 제품ID로 조회 후 DB저장'
     * @param array $offerIds '제품ID'
     * @param string $type '요청 페이지'
     * @return void
     */
    abstract function collectProduct(array $offerIds, string $type = LogConstant::COLLECT_API_KEYWORDQUERY): void;

    /**
     * @func productsUpdateImages
     * @description '상품 이미지 업데이트'
     * @param int $offerId
     * @param array $images
     * @return array
     */
    abstract function productsUpdateImages(int $offerId, array $images): array;
}
