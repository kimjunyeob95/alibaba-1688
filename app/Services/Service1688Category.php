<?php

namespace App\Services;

use App\Abstracts\CategoryAbstract;

class Service1688Category
{
   private CategoryAbstract $categoryAbstract;

   public function __construct(
      CategoryAbstract $categoryAbstract
   )
   {
      $this->categoryAbstract = $categoryAbstract;
   }

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
     * @func save1688AllCategory
     * @description '1688 모든 최상위 카테고리 저장'
     * @return void
   */
   public function save1688AllCategory(): void
   {
      $this->categoryAbstract->save1688AllCategory();
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
     * @func saveWCategory
     * @description 'w_categories 카테고리 테이블로 insert'
     * @return void
   */
   public function saveWCategory(): void
   {
      $this->categoryAbstract->saveWCategory();
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
     * @func weightList
     * @description '표준 중량(배송비) 관리'
     * @param array $params
     * @return array
   */
   public function weightList(array $params): array
   {
      return $this->categoryAbstract->weightList($params);
   }

   /**
     * @func sendMallList
     * @description '전송 카테고리 관리'
     * @param array $params
     * @return array
   */
   public function sendMallList(array $params): array
   {
      return $this->categoryAbstract->sendMallList($params);
   }

   /**
     * @func getW
     * @description 'W 카테고리 조회'
     * @param array $params
     * @return array
   */
   public function getW(array $params): array
   {
      return $this->categoryAbstract->getW($params);
   }

   /**
     * @func wMapping
     * @description 'W 카테고리 맵핑'
     * @param array $params
     * @return array
   */
   public function wMapping(array $params): array
   {
      return $this->categoryAbstract->wMapping($params);
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

   /**
     * @func getWDepth
     * @description 'W 하위 카테고리 조회'
     * @param array $params
     * @return array
   */
   public function getWDepth(array $params): array
   {
      return $this->categoryAbstract->getWDepth($params);
   }

   /**
     * @func getInfos
     * @description '카테고리 정보 조회'
     * @param array $categoryIds '카테고리 ID'
     * @return array
   */
   public function getInfos(array $categoryIds): array
   {
      return $this->categoryAbstract->getInfos($categoryIds);
   }

   /**
     * @func saveCategoryTree
     * @description 'categories 테이블의 데이터들을 category_trees 테이블로 정리'
     * @return void
   */
   public function saveCategoryTree(): void
   {
      $this->categoryAbstract->saveCategoryTree();
   }

   /**
     * @func weightSave
     * @description '카테고리 중량 저장'
     * @param array $categoryIds '카테고리 ID'
     * @param float $weight '표준 중량'
     * @return array
   */
   public function weightSave(array $categoryIds, float $weight): array
   {
      return $this->categoryAbstract->weightSave($categoryIds, $weight);
   }

   /**
     * @func weightRemove
     * @description '카테고리 중량 삭제'
     * @param array $categoryIds '카테고리 ID'
     * @return array
   */
   public function weightRemove(array $categoryIds): array
   {
      return $this->categoryAbstract->weightRemove($categoryIds);
   }

   /**
     * @func sendMallUpdate
     * @description '채널별 전송 카테고리 수정'
     * @param array $cateParams '카테고리 정보'
     * @return array
   */
   public function sendMallUpdate(array $cateParams): array
   {
      return $this->categoryAbstract->sendMallUpdate($cateParams);
   }

   /**
     * @func topList
     * @description 'W 카테고리별 인기상품 조회'
     * @param int $categoryId '카테고리 ID'
     * @param string $country '언어'
     * @param int $pageSize '페이징 수'
     * @return array
   */
   public function topList(int $categoryId, string $country, int $pageSize): array
   {
      return $this->categoryAbstract->topList($categoryId, $country, $pageSize);
   }

   /**
     * @func topKeyword
     * @description 'W 카테고리별 인기검색어 조회'
     * @param int $categoryId '카테고리 ID'
     * @param string $country '언어'
     * @return array
   */
   public function topKeyword(int $categoryId, string $country): array
   {
      return $this->categoryAbstract->topKeyword($categoryId, $country);
   }
}