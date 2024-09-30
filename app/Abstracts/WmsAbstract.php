<?php

namespace App\Abstracts;

use App\Packages\Bonaera;

abstract class WmsAbstract
{
    protected array $returnMsg;
    protected string $unipass_token;
    protected Bonaera $bonaera;

    public function __construct(Bonaera $bonaera)
    {
        $this->returnMsg     = helpers_fail_message();
        $this->bonaera       = $bonaera;
        $this->unipass_token = env("UNIPASS_TOKEN", "g230z224g099q163r080k040u0");
    }

    /**
    * @func hsCodeList
    * @description 'HS code 라스트'
    * @param array $params
    * @return array
    */
    abstract function hsCodeList(array $params): array;

    /**
    * @func inList
    * @description '입고관리 라스트'
    * @param array $params
    * @return array
    */
    abstract function inList(array $params): array;

    /**
    * @func apiHsCodeList
    * @description 'HS code 라스트'
    * @param array $params
    * @return array
    */
    public function apiHsCodeList(array $params): array
    {
        $result    = $this->hsCodeList($params);
        $paginator = $result["data"];
        $datas     = [];
        foreach ($paginator as $prdObj) {
           $datas[] = $prdObj;
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
    * @func getTariff
    * @description '관세율조회'
    * @param string $hsCode
    * @return array
    */
    abstract function getTariff(string $hsCode): array;

    /**
    * @func bonaeraInUpdate
    * @description '입고정보 업데이트'
    * @param int $id
    * @return void
    */
    abstract function bonaeraInUpdate(int $id): void;

    /**
    * @func bonaeraOutUpdate
    * @description '출고정보 업데이트'
    * @param int $id
    * @return void
    */
    abstract function bonaeraOutUpdate(int $id): void;

    /**
    * @func inDetail
    * @description '입고정보 상세'
    * @param string $stockNo
    * @return array
    */
    abstract function inDetail(string $stockNo): array;

    /**
    * @func outList
    * @description '출고관리 라스트'
    * @param array $params
    * @return array
    */
    abstract function outList(array $params): array;
}
