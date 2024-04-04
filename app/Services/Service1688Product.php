<?php

namespace App\Services;

use App\Abstracts\CategoryAbstract;
use App\Abstracts\ProductAbstract;
use App\Constants\LogConstant;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

class Service1688Product
{
   private CategoryAbstract $categoryAbstract;
   private ProductAbstract $productAbstract;

   public function __construct(
      CategoryAbstract $categoryAbstract,
      ProductAbstract $productAbstract
   )
   {
      $this->categoryAbstract = $categoryAbstract;
      $this->productAbstract  = $productAbstract;
   }

   /**
    * @func getPrdList
    * @description '1688 수집 상품 리스트'
    * @param array $params
    * @return array
    */
   public function getPrdList(array $params): array
   {
      return $this->productAbstract->getPrdList($params);
   }

   /**
    * @func getPrdList
    * @description '1688API 수집 상품 디테일'
    * @param int $offerId
    * @return array
    */
   public function getPrdDetail(int $offerId): array
   {
      return $this->productAbstract->getPrdDetail($offerId);
   }

   /**
     * @func getQueryProductDetail
     * @description '상품ID로 조회'
     * @param array $offerIds
     * @return array
     */
   public function getQueryProductDetail(array $offerIds): array
   {
      return $this->productAbstract->getQueryProductDetail($offerIds);
   }

   /**
     * @func getKeywordQuery
     * @description '상품 기본 정보로 조회'
     * @param array $params
     * @return array
     */
   public function getKeywordQuery(array $params): array
   {
      return $this->productAbstract->getKeywordQuery($params);
   }

   /**
     * @func getImageQuery
     * @description '상품 이미지로 조회'
     * @param array $params
     * @return array
     */
   public function getImageQuery(array $params): array
   {
      return $this->productAbstract->getImageQuery($params);
   }

   /**
     * @func getPrdCollectLogList
     * @description '상품 수집현황 조회'
     * @param array $params
     * @return LengthAwarePaginator
     */
   public function getPrdCollectLogList(array $params): LengthAwarePaginator
   {
      return $this->productAbstract->getPrdCollectLogList($params);
   }

   /**
     * @func createImgId
     * @description '1688 이미지ID 생성'
     * @param UploadedFile $file
     * @return array
     */
   public function createImgId(UploadedFile $file): array
   {
      return $this->productAbstract->createImgId($file);
   }

   /**
     * @func prdCollectLogDetail
     * @description '상품 수집 현황 조회'
     * @param int $logId
     * @return array
     */
   public function prdCollectLogDetail(int $logId): array
   {
      return $this->productAbstract->prdCollectLogDetail($logId);
   }

   /**
     * @func getMallCategory
     * @description '1688API 상품상세 endPoint 조회'
     * @param int $offerId '제품ID'
     * @return array
     */
   public function getProductData(int $offerId): array
   {
      return $this->productAbstract->getProductData($offerId);
   }

   /**
     * @func collectProduct
     * @description '1688API 제품ID로 조회 후 DB저장'
     * @param array $offerIds '제품ID'
     * @param string $type '요청 페이지'
     * @return void
     */
   public function collectProduct(array $offerIds, string $type = LogConstant::COLLECT_API_KEYWORDQUERY): void
   {
      $this->productAbstract->collectProduct($offerIds, $type);
   }

   /**
     * @func saveMallProductByCategotyId
     * @description '1688API 카테고리ID별 상품수집'
     * @param int $categoryId '카테고리ID'
     * @return void
     */
   public function saveMallProductByCategotyId(int $categoryId): void
   {
      $this->productAbstract->saveMallProductByCategotyId($categoryId);
   }

   /**
     * @func saveMallProductByImageId
     * @description '1688API 이미지ID로 상품 수집'
     * @param int $string 'imageId'
     * @return void
     */
   public function saveMallProductByImageId(string $imageId): void
   {
      $this->productAbstract->saveMallProductByImageId($imageId);
   }

   /**
     * @func saveKeywordQuery
     * @description '1688API keywordQueryAPI로 상품 수집'
     * @param array $params
     * @return array
     */
   public function saveKeywordQuery(array $params): array
   {
      return $this->productAbstract->saveKeywordQuery($params);
   }

   /**
     * @func saveImageQuery
     * @description '1688API imageQueryAPI로 상품 수집'
     * @param array $params
     * @return array
     */
   public function saveImageQuery(array $params): array
   {
      return $this->productAbstract->saveImageQuery($params);
   }


   /* =========================================== 카테고리 Abstract =================================================================================== */

   /**
     * @func getAllCategory
     * @description '수집 한 카테고리를 단계별로 정리한 데이터 목록'
     * @return array
     */
   public function getAllCategory(): array
   {
      return $this->categoryAbstract->getAllCategory();
   }

   /**
     * @func getTreeCategory
     * @description '수집 한 최상위 카테고리 단위를 계층별 목록으로 반환'
     * @param int $categoryId '카테고리 ID'
     * @return array
     */
   public function getTreeCategory(int $categoryId): array
   {
      return $this->categoryAbstract->getTreeCategory($categoryId);
   }

   /**
     * @func getMappingCategory
     * @description '1688<->채널 카테고리 맵핑 조회'
     * @param string $channel
     * @return array
     */
   public function getMappingCategory(string $channel): array
   {
      return $this->categoryAbstract->getMappingCategory($channel);
   }

   /**
     * @func getMallCategory
     * @description '오픈API 카테고리 endPoint 조회'
     * @param int $categoryId '카테고리 ID'
     * @return array
     */
   public function getMallCategory(int $categoryId): array
   {
      return $this->categoryAbstract->getMallCategory($categoryId);
   }

   /**
     * @func saveCategory
     * @description '1688API 카테고리 endPoint 조회 후 저장'
     * @return void
     */
   public function saveCategory(): void
   {
      $this->categoryAbstract->saveCategory();
   }

   /**
     * @func saveCategoryMapping
     * @description 'categories 테이블의 데이터들을 category_mappings 테이블로 정리'
     * @return void
     */
   public function saveCategoryMapping(): void
   {
      $this->categoryAbstract->saveCategoryMapping();
   }
}