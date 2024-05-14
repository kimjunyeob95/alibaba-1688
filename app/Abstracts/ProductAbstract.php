<?php

namespace App\Abstracts;

use App\Constants\ForbiddenWordConstant;
use App\Constants\LogConstant;
use Illuminate\Database\Eloquent\Collection;
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
    * @func getPrdExceptList
    * @description '판매제외 상품 리스트'
    * @param array $params
    * @return array
    */
    abstract function getPrdExceptList(array $params): array;

    /**
     * @func apiPrdDetail
     * @description 'w API 상품 상세 조회'
     * @param int $offerId
     * @return array
    */
    abstract function apiPrdDetail(int $offerId): array;

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
     * @param array $params
     * @return LengthAwarePaginator
     */
    abstract function getUrlQuery(array $params): LengthAwarePaginator;

    /**
     * @func urlQueryDetail
     * @description '상품URL 조회 상세'
     * @param int $searchId
     * @return array
     */
    abstract function urlQueryDetail(int $searchId): array;

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

    /**
     * @func saveProductSearchData
     * @description '상품상세 URL로 조회 요청'
     * @param array $params
     * @return void
     */
    abstract function saveProductSearchData(array $params): void;

    /**
     * @func urlQueryDel
     * @description '상품상세 URL 수집 데이터 삭제'
     * @param array $ids
     * @return array
    */
    abstract function urlQueryDel(array $ids): array;

    /**
     * @func getPrdImageEdit
     * @description '상품 이미지 수정'
     * @param int $offerId '제품ID'
     * @return array
     */
    abstract function getPrdImageEdit(int $offerId): array;

    /**
     * @func wAppProductMapping
     * @description 'wapp 상품 미맵핑 컬럼 업데이트'
     * @return void
    */
    abstract function wAppProductMapping(): void;

    /**
     * @func imageExcept
     * @description '이미지 수집 제외 처리'
     * @param array $imgIds
     * @param string $is_except
     * @return array
    */
    abstract function imageExcept(array $imgIds, string $is_except): array;

    /**
     * @func imageAccept
     * @description 'AI 이미지 적용'
     * @param array $aiImgIds
     * @return array
    */
    abstract function imageAccept(array $aiImgIds): array;

    /**
     * @func mdPriceUpdate
     * @description 'MD price 수정'
     * @param array $offerIds
     * @param int $mdPrice
     * @return array
    */
    abstract function mdPriceUpdate(array $offerIds, int $mdPrice): array;

    /**
     * @func statusUpdate
     * @description '판매상태 변경'
     * @param array $offerIds
     * @param string $status
     * @return array
    */
    abstract function statusUpdate(array $offerIds, string $status): array;

    /**
     * @func imageMainApply
     * @description '대표 이미지 적용'
     * @param array $aiImgIds
     * @return array
    */
    abstract function imageMainApply(array $aiImgIds): array;

    /**
     * @func gosiExcept
     * @description '고시정보 제외 처리'
     * @param array $gosiList
     * @return array
    */
    abstract function gosiExcept(array $gosiList): array;

    /**
     * @func update
     * @description '상품 update'
     * @param array $params
     * @return array
    */
    abstract function update(array $params): array;

    /**
     * @func inspectStatusUpdate
     * @description '검수상태 update'
     * @param array $params
     * @return array
    */
    abstract function inspectStatusUpdate(array $params): array;

    /**
     * @func removeForbiddenText
     * @description '삭제어 처리'
     * @param Collection $removeForbiddenWords
     * @param string $text
     * @return string
     */
    function removeForbiddenText(Collection $removeForbiddenWords, string $text, string $apply_type): string
    {
        foreach ($removeForbiddenWords as $delObj) {
            if( $delObj->apply_type == ForbiddenWordConstant::KEYWORD_APPLY_ALL || $delObj->apply_type == $apply_type ){
                $removeWord = $delObj->target_keyword;
    
                // 1. 삭제어 앞과 뒤에 공백이 없는 경우 삭제어만 삭제
                //    예: "HelloWord"에서 "Word"를 삭제 -> "Hello"
                $pattern1 = '/(?<!\s)' . preg_quote($removeWord, '/') . '(?!\s)/';
                if (preg_match($pattern1, $text)) {
                    $text = preg_replace($pattern1, '', $text);
                }
    
                // 2. 삭제어 앞 또는 뒤에 공백이 있는 경우 삭제어만 삭제
                //    예: "Hello Word "에서 "Word"를 삭제 -> "Hello "
                $pattern2 = '/(?<=\s)' . preg_quote($removeWord, '/') . '(?!\s)|(?<!\s)' . preg_quote($removeWord, '/') . '(?=\s)/';
                if (preg_match($pattern2, $text)) {
                    $text = preg_replace($pattern2, '', $text);
                }
    
                // 3. 삭제어 앞과 뒤에 공백이 있는 경우 하나의 공백과 삭제어만 삭제
                //    예: "Hello Word Test"에서 "Word"를 삭제 -> "Hello Test"
                $pattern3 = '/\s+' . preg_quote($removeWord, '/') . '\s+/';
                if (preg_match($pattern3, $text)) {
                    $text = preg_replace($pattern3, ' ', $text);
                }
            }
        }

        return $text;
    }

    /**
     * @func replaceForbiddenText
     * @description '교체어 처리'
     * @param Collection $replaceForbiddenWords
     * @param string $text
     * @return string
     */
    function replaceForbiddenText(Collection $replaceForbiddenWords, string $text, string $apply_type): string
    {
        foreach ($replaceForbiddenWords as $replaceObj) {
            if( $replaceObj->apply_type == ForbiddenWordConstant::KEYWORD_APPLY_ALL || $replaceObj->apply_type == $apply_type ){

                $originWord  = $replaceObj->target_keyword;
                $replaceWord = $replaceObj->replace_keyword;

                // 1. 해당 텍스트가 originWord에 걸릴 시 replaceWord로 교체
                $text = str_replace($originWord, $replaceWord, $text);
            }
        }

        return $text;
    }
}
