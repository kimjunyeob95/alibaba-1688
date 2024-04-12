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
     * @func getUrlQuery
     * @description '상품상세 URL로 수집'
     * @param array $params
     * @return LengthAwarePaginator
   */
   public function getUrlQuery(array $params): LengthAwarePaginator
   {
      return $this->productAbstract->getUrlQuery($params);
   }

   /**
    * @func urlQueryDetail
    * @description '상품URL 조회 상세'
    * @param int $searchId
    * @return array
   */
   public function urlQueryDetail(int $searchId): array
   {
      return $this->productAbstract->urlQueryDetail($searchId);
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

   /**
     * @func apiPrdList
     * @description 'w API 상품리스트'
     * @param array $params
     * @return array
   */
   public function apiPrdList(array $params): array
   {
      $result    = $this->productAbstract->getPrdList($params);
      $paginator = $result["paginator"];
      $datas     = [];
      foreach ($paginator as $prdObj) {
         $data = [
            "offer_id"       => $prdObj->offer_id,
            "prd_name"       => $prdObj->prd_name,
            "prd_name_trans" => $prdObj->prd_name_trans
         ];

         foreach ($prdObj->options as $option) {
            $data["options"][] = [
               "id"                => $option->id,
               "option_name"       => $option->option_name,
               "option_name_trans" => $option->option_name_trans,
            ];
         }

         foreach ($prdObj->images as $image) {
            $data["images"][] = [
               "id"             => $image->id,
               "img_type"       => $image->img_type,
               "img_url_origin" => $image->img_url_origin,
               "img_url_trans"  => $image->img_url_trans,
            ];
         }

         $datas[] = $data;
      }

      $lastPage  = $paginator->lastPage();
      $page      = $paginator->currentPage();
      $pageSize  = $paginator->perPage();

      return [
         "result"   => $datas,
         "lastPage" => $lastPage,
         "page"     => $page,
         "pageSize" => (int)$pageSize,
      ];
   }

   /**
     * @func productsUpdateImages
     * @description '상품 이미지 업데이트'
     * @param int $offerId
     * @param array $images
     * @return array
   */
   public function productsUpdateImages(int $offerId, array $images): array
   {
      return $this->productAbstract->productsUpdateImages($offerId, $images);
   }

   /**
     * @func saveProductSearchData
     * @description '상품상세 URL로 조회 요청'
     * @param array $params
     * @return void
   */
   public function saveProductSearchData(array $params): void
   {
      $this->productAbstract->saveProductSearchData($params);
   }

   /**
     * @func urlQueryDel
     * @description '상품상세 URL 수집 데이터 삭제'
     * @param array $ids
     * @return array
   */
   public function urlQueryDel(array $ids): array
   {
      return $this->productAbstract->urlQueryDel($ids);
   }


   /* =========================================== 카테고리 Abstract =================================================================================== */

   /**
     * @func getAllCategory
     * @description '수집 한 카테고리를 단계별로 정리한 데이터 목록'
     * @return array
     */
   public function getAllCategory(mixed $parent_cate_id): array
   {
      return $this->categoryAbstract->getAllCategory($parent_cate_id);
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

   /**
     * @func saveWCategoryMapping
     * @description 'categories 테이블의 데이터들을 w_categories 테이블로 정리'
     * @return void
   */
   public function saveWCategoryMapping(): void
   {
      $this->categoryAbstract->saveWCategoryMapping();
   }

   /**
     * @func cateList
     * @description '카테고리 리스트'
     * @param array $params
     * @return array
   */
   public function cateList(array $params): array
   {
      return $this->categoryAbstract->cateList($params);
   }

   /**
     * @func getDepth
     * @description '하위 카테고리 조회'
     * @param int $categoryId '카테고리 ID'
     * @return array
   */
   public function getDepth(int $categoryId): array
   {
      return $this->categoryAbstract->getDepth($categoryId);
   }
}