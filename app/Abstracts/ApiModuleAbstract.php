<?php

namespace App\Abstracts;

abstract class ApiModuleAbstract
{
    protected array $returnMsg;
    protected string $apiDomain;

    public function __construct(string $apiDomain)
    {
        $this->returnMsg = helpers_fail_message();
        $this->apiDomain = $apiDomain;
    }
    
    /**
     * @func getAllCategory
     * @description '수집 한 카테고리를 단계별로 정리한 데이터 목록'
     */
    abstract function getAllCategory(): array;

    /**
     * @func getTreeCategory
     * @description '수집 한 최상위 카테고리 단위를 계층별 목록으로 반환'
     * @param int $categoryId '카테고리 ID'
     */
    abstract function getTreeCategory(int $categoryId): array;
    
    /**
     * @func getMallCategory
     * @description '오픈API 카테고리 endPoint 조회'
     * @param int $categoryId '카테고리 ID'
     */
    abstract function getMallCategory(int $categoryId): array;

    /**
     * @func saveMallProductByCategotyId
     * @description '오픈API 카테고리ID별 상품수집'
     */
    abstract function saveMallProductByCategotyId(int $categoryId): void;

    /**
     * @func getMappingCategory
     * @description '1688<->채널 카테고리 맵핑 조회'
     */
    abstract function getMappingCategory(string $channel): array;
    
    /**
     * @func apiCurl
     * @description 'curl 통신 method'
     * @param string $method
     * @param string $endPoint
     * @param array $payload
     * @param array $header
     */
    abstract function apiCurl(string $method, string $endPoint, array $payload, array $header): array;
}
