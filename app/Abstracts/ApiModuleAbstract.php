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

    abstract function getAllCategory(int $categoryId): array;
    abstract function getCategory(int $categoryId): array;
    abstract function apiCurl(string $method, string $endPoint, array $payload, array $header): array;
}
