<?php

namespace App\Abstracts;

use App\Constants\Constant1688;
use Exception;

abstract class CategoryAbstract
{
    protected array $returnMsg;
    protected string $accessToken;

    public function __construct()
    {
        $this->returnMsg   = helpers_fail_message();
        $this->accessToken = env("1688_ACCESS_TOKEN");
    }

    /**
     * @func getAllCategory
     * @description '수집 한 카테고리를 단계별로 정리한 데이터 목록'
     * @param mixed $parent_cate_id
     * @return array
     */
    abstract function getAllCategory(mixed $parent_cate_id): array;

    /**
     * @func getTreeCategory
     * @description '수집 한 최상위 카테고리 단위를 계층별 목록으로 반환'
     * @param int $categoryId '카테고리 ID'
     * @return array
     */
    abstract function getTreeCategory(int $categoryId): array;
    
    /**
     * @func getMallCategory
     * @description '오픈API 카테고리 endPoint 조회'
     * @param int $categoryId '카테고리 ID'
     * @return array
     */
    abstract function getMallCategory(int $categoryId): array;

    /**
     * @func getMappingCategory
     * @description '1688<->채널 카테고리 맵핑 조회'
     * @param string $channel
     * @return array
     */
    abstract function getMappingCategory(string $channel): array;

    /**
     * @func save1688AllCategory
     * @description '1688 모든 최상위 카테고리 저장'
     * @return void
     */
    abstract function save1688AllCategory(): void;

    /**
     * @func saveCategory
     * @description '1688API 카테고리 endPoint 조회 후 저장'
     * @return void
     */
    abstract function saveCategory(): void;

    /**
     * @func saveCategoryMapping
     * @description 'categories 테이블의 데이터들을 category_mappings 테이블로 정리'
     * @return void
     */
    abstract function saveCategoryMapping(): void;

    /**
     * @func saveWCategory
     * @description 'w_categories 카테고리 테이블로 insert'
     * @return void
     */
    abstract function saveWCategory(): void;

    /**
     * @func saveWCategoryMapping
     * @description 'categories 테이블의 데이터들을 w_categories 테이블로 정리'
     * @return void
     */
    abstract function saveWCategoryMapping(): void;

    /**
     * @func cateList
     * @description '카테고리 리스트'
     * @param array $params
     * @return array
    */
    abstract function cateList(array $params): array;

    /**
     * @func weightList
     * @description '표준 중량(배송비) 관리'
     * @param array $params
     * @return array
    */
    abstract function weightList(array $params): array;

    /**
     * @func getW
     * @description 'W 카테고리 조회'
     * @param array $params
     * @return array
     */
    abstract function getW(array $params): array;

    /**
     * @func wMapping
     * @description 'W 카테고리 맵핑'
     * @param array $params
     * @return array
     */
    abstract function wMapping(array $params): array;

    /**
     * @func getDepth
     * @description '하위 카테고리 조회'
     * @param int $categoryId '카테고리 ID'
     * @return array
     */
    abstract function getDepth(int $categoryId): array;

    /**
     * @func getWDepth
     * @description 'W 하위 카테고리 조회'
     * @param array $params
     * @return array
     */
    abstract function getWDepth(array $params): array;

    /**
     * @func getInfos
     * @description '카테고리 정보 조회'
     * @param array $categoryIds '카테고리 ID'
     * @return array
     */
    abstract function getInfos(array $categoryIds): array;

    /**
     * @func saveCategoryTree
     * @description 'categories 테이블의 데이터들을 category_trees 테이블로 정리'
     * @return void
    */
    abstract function saveCategoryTree(): void;

    /**
     * @func weightSave
     * @description '카테고리 중량 저장'
     * @param array $categoryIds '카테고리 ID'
     * @param int $weight '표준 중량'
     * @return array
    */
    abstract function weightSave(array $categoryIds, int $weight): array;

    /**
     * @func weightRemove
     * @description '카테고리 중량 삭제'
     * @param array $categoryIds '카테고리 ID'
     * @return array
    */
    abstract function weightRemove(array $categoryIds): array;

    /**
     * @func topList
     * @description 'W 카테고리별 인기상품 조회'
     * @param int $categoryId '카테고리 ID'
     * @return array
    */
    public function topList(int $categoryId): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.topList.query/";
            $payload = [
                'access_token'     => $this->accessToken,
                'rankQueryParams' => [
                    'rankId'   => $categoryId,
                    'rankType' => Constant1688::RANK_TYPE_COMPLEX,
                    'limit'    => 10,
                    'language' => Constant1688::LANGUAGE_KO,
                ]
            ];
            $result = curl_1688("post", $endPoint, $payload);
            if( isset($result["data"]["result"]) ){
                $returnMsg = helpers_success_message($result["data"]["result"]);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
