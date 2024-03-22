<?php

namespace App\Services;

use App\Abstracts\CategoryAbstract;
use App\Abstracts\OpenApiAbstract;
use App\Abstracts\ProductAbstract;

class Service1688
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
     * @func getAllCategory
     * @description '1688에서 수집 한 카테고리를 단계별로 정리한 데이터 목록'
     */
    public function getAllCategory(): array
    {
       return $this->categoryAbstract->getAllCategory();
    }

    /**
     * @func getTreeCategory
     * @description '1688에서 수집 한 최상위 카테고리 단위를 계층별 목록으로 반환'
     * @param int $categoryId '카테고리 ID'
     */
    public function getTreeCategory(int $categoryId): array
    {
        return $this->categoryAbstract->getTreeCategory($categoryId);
    }

    /**
     * @func getMappingCategory
     * @description '1688<->채널 카테고리 맵핑 조회'
     * @param string $channel
     */
    public function getMappingCategory(string $channel): array
    {
        return $this->categoryAbstract->getMappingCategory($channel);
    }

    /**
     * @func getMallCategory
     * @description '1688 카테고리 endPoint 조회'
     * @param int $categoryId '카테고리 ID'
     */
    public function getMallCategory(int $categoryId): array
    {
        return $this->categoryAbstract->getMallCategory($categoryId);
    }

    /**
     * @func getProductData
     * @description '1688 상품상세 endPoint 조회'
     * @param int $offerId '제품ID'
     */
    public function getProductData(int $offerId): array
    {
        return $this->productAbstract->getProductData($offerId);
    }
}