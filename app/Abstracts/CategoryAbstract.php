<?php

namespace App\Abstracts;

abstract class CategoryAbstract
{
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
}
