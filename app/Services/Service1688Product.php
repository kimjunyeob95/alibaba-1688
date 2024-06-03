<?php

namespace App\Services;

use App\Abstracts\ProductAbstract;
use App\Constants\LogConstant;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

class Service1688Product
{
   private ProductAbstract $productAbstract;
   private ProductAbstract $productAbstractW2;

   public function __construct(
      ProductAbstract $productAbstract,
      ProductAbstract $productAbstractW2
   )
   {
      $this->productAbstract   = $productAbstract;
      $this->productAbstractW2 = $productAbstractW2;
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
    * @func getPrdExceptList
    * @description '판매제외 상품 리스트'
    * @param array $params
    * @return array
   */
   public function getPrdExceptList(array $params): array
   {
      return $this->productAbstract->getPrdExceptList($params);
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
            "offer_id"    => $prdObj->offer_id,
            "prd_name"    => $prdObj->prd_name,
            "prd_name_kr" => $prdObj->prd_name_kr
         ];

         foreach ($prdObj->options as $option) {
            $data["options"][] = [
               "id"             => $option->id,
               "option_name"    => $option->option_name,
               "option_name_kr" => $option->option_name_kr,
            ];
         }
         foreach ($prdObj->images as $key => $image) {
            $data["images"][$key] = [
               "id"             => $image->id,
               "img_type"       => $image->img_type,
               "is_except"      => $image->is_except,
               "img_url_origin" => $image->img_url_origin,
               "img_url_trans"  => $image->img_url_trans,
            ];

            foreach ($image->ai_all_imgs as $ai_img) {
               $data["images"][$key]["ai_images"][] = [
                  "id"         => $ai_img->id,
                  "img_id"     => $ai_img->img_id,
                  "is_origin"  => $ai_img->is_origin,
                  "img_url_ai" => $ai_img->img_url_ai,
                  "created_at" => $ai_img->created_at,
               ]; 
            }
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
     * @func apiPrdDetail
     * @description 'w API 상품 상세 조회'
     * @param int $offerId
     * @return array
   */
   public function apiPrdDetail(int $offerId): array
   {
      return $this->productAbstract->apiPrdDetail($offerId);
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

   /**
     * @func getPrdImageEdit
     * @description '상품 이미지 수정'
     * @param int $offerId
     * @return array
   */
   public function getPrdImageEdit(int $offerId): array
   {
      return $this->productAbstract->getPrdImageEdit($offerId);
   }

   /**
     * @func wAppProductMapping
     * @description 'wapp 상품 미맵핑 컬럼 업데이트'
     * @return void
   */
   public function wAppProductMapping(): void
   {
      $this->productAbstract->wAppProductMapping();
   }

   /**
     * @func imageExcept
     * @description '이미지 수집 제외 처리'
     * @param array $imgIds
     * @param string $is_except
     * @return array
   */
   public function imageExcept(array $imgIds, string $is_except): array
   {
      return $this->productAbstract->imageExcept($imgIds, $is_except);
   }

   /**
     * @func imageAccept
     * @description 'AI 이미지 적용'
     * @param array $aiImgIds
     * @return array
   */
   public function imageAccept(array $aiImgIds): array
   {
      return $this->productAbstract->imageAccept($aiImgIds);
   }

   /**
     * @func mdPriceUpdate
     * @description 'MD price 수정'
     * @param array $offerIds
     * @param int $mdPrice
     * @return array
   */
   public function mdPriceUpdate(array $offerIds, int $mdPrice): array
   {
      return $this->productAbstract->mdPriceUpdate($offerIds, $mdPrice);
   }

   /**
     * @func statusUpdate
     * @description '판매상태 변경'
     * @param array $offerIds
     * @param string $status
     * @return array
   */
   public function statusUpdate(array $offerIds, string $status): array
   {
      return $this->productAbstract->statusUpdate($offerIds, $status);
   }

   /**
     * @func imageMainApply
     * @description '대표 이미지 적용'
     * @param array $aiImgIds
     * @return array
   */
   public function imageMainApply(array $aiImgIds): array
   {
      return $this->productAbstract->imageMainApply($aiImgIds);
   }

   /**
     * @func gosiExcept
     * @description '고시정보 제외 처리'
     * @param array $gosiList
     * @return array
   */
   public function gosiExcept(array $gosiList): array
   {
      return $this->productAbstract->gosiExcept($gosiList);
   }

   /**
     * @func updateW1
     * @description '상품 update'
     * @param array $params
     * @return array
   */
   public function updateW1(array $params): array
   {
      return $this->productAbstract->update($params);
   }

   /**
     * @func inspectStatusUpdate
     * @description '검수상태 update'
     * @param array $params
     * @return array
   */
   public function inspectStatusUpdate(array $params): array
   {
      return $this->productAbstract->inspectStatusUpdate($params);
   }

   /**
     * @func weightSave
     * @description '상품 중량 저장'
     * @param array $offerIds '제품 ID'
     * @param int $weight '표준 중량'
     * @return array
   */
   public function weightSave(array $offerIds, int $weight): array
   {
      return $this->productAbstract->weightSave($offerIds, $weight);
   }

   /**
     * @func noticeNameUpdate
     * @description '정보고시 적용 항목명 update'
     * @param array $attributeIds
     * @param string $applyAttributeName
     * @return array
   */
   public function noticeNameUpdate(array $attributeIds, string $applyAttributeName = ""): array
   {
      return $this->productAbstract->noticeNameUpdate($attributeIds, $applyAttributeName);
   }

   /****************************************** WApp W2 **********************************************/

   /**
     * @func getQueryProductDetailW2
     * @description '상품ID로 조회'
     * @param array $offerIds
     * @return array
   */
   public function getQueryProductDetailW2(array $offerIds): array
   {
      return $this->productAbstractW2->getQueryProductDetail($offerIds);
   }

   /**
     * @func collectProductW2
     * @description '1688API 제품ID로 조회 후 DB저장'
     * @param array $offerIds '제품ID'
     * @param string $type '요청 페이지'
     * @return void
   */
   public function collectProductW2(array $offerIds, string $type = LogConstant::COLLECT_API_KEYWORDQUERY): void
   {
      $this->productAbstractW2->collectProduct($offerIds, $type);
   }

   /**
     * @func getPrdCollectLogListW2
     * @description '상품 수집현황 조회'
     * @param array $params
     * @return LengthAwarePaginator
   */
   public function getPrdCollectLogListW2(array $params): LengthAwarePaginator
   {
      return $this->productAbstractW2->getPrdCollectLogList($params);
   }

   /**
    * @func getPrdListW2
    * @description '1688 수집 상품 리스트'
    * @param array $params
    * @return array
   */
   public function getPrdListW2(array $params): array
   {
      return $this->productAbstractW2->getPrdList($params);
   }

   /**
    * @func getPrdDetailW2
    * @description '1688API 수집 상품 디테일'
    * @param int $offerId
    * @return array
   */
   public function getPrdDetailW2(int $offerId): array
   {
      return $this->productAbstractW2->getPrdDetail($offerId);
   }

   /**
     * @func statusUpdateW2
     * @description '판매상태 변경'
     * @param array $offerIds
     * @param string $status
     * @return array
   */
   public function statusUpdateW2(array $offerIds, string $status): array
   {
      return $this->productAbstractW2->statusUpdate($offerIds, $status);
   }

   /**
     * @func updateW2
     * @description '상품 update'
     * @param array $params
     * @return array
   */
   public function updateW2(array $params): array
   {
      return $this->productAbstractW2->update($params);
   }
}