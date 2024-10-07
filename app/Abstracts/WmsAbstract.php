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
    * @func inFailList
    * @description '입고 실패 리스트'
    * @param array $params
    * @return array
    */
    abstract function inFailList(array $params): array;

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
           "result"    => $datas,
           "last_page" => $lastPage,
           "page"      => $page,
           "page_size" => (int)$pageSize,
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
    * @func bonaeraInFailHscodeUpdate
    * @description '입고실패 HS code 적용'
    * @param array $ids
    * @param string $hsCode
    * @return array
    */
    abstract function bonaeraInFailHscodeUpdate(array $ids, string $hsCode): array;

    /**
    * @func bonaeraInCreate
    * @description '입고신청'
    * @param int $id
    * @return void
    */
    abstract function bonaeraInCreate(int $id): void;

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

    /**
    * @func outDetail
    * @description '출고정보 상세'
    * @param string $groupNo
    * @return array
    */
    abstract function outDetail(string $groupNo): array;
}
