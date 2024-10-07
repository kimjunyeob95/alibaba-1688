<?php

namespace App\Services\Wms;

use App\Abstracts\WmsAbstract;

class WmsService
{
   private WmsAbstract $wmsAbstract;

   public function __construct(
      WmsAbstract $wmsAbstract
   )
   {
      $this->wmsAbstract = $wmsAbstract;
   }

   /**
    * @func hsCodeList
    * @description 'HS code 라스트'
    * @param array $params
    * @return array
    */
   public function hsCodeList(array $params): array
   {
      return $this->wmsAbstract->hsCodeList($params);
   }

   /**
    * @func inList
    * @description '입고관리 라스트'
    * @param array $params
    * @return array
    */
   public function inList(array $params): array
   {
      return $this->wmsAbstract->inList($params);
   }

   /**
    * @func apiHsCodeList
    * @description 'HS code 라스트'
    * @param array $params
    * @return array
    */
    public function apiHsCodeList(array $params): array
    {
       return $this->wmsAbstract->apiHsCodeList($params);
    }

   /**
    * @func getTariff
    * @description '관세율조회'
    * @param string $hsCode
    * @return array
    */
   public function getTariff(string $hsCode): array
   {
      return $this->wmsAbstract->getTariff($hsCode);
   }

   /**
    * @func bonaeraInUpdate
    * @description '입고정보 업데이트'
    * @param int $id
    * @return void
    */
   public function bonaeraInUpdate(int $id): void
   {
      $this->wmsAbstract->bonaeraInUpdate($id);
   }

   /**
    * @func bonaeraOutUpdate
    * @description '출고정보 업데이트'
    * @param int $id
    * @return void
    */
   public function bonaeraOutUpdate(int $id): void
   {
      $this->wmsAbstract->bonaeraOutUpdate($id);
   }

   /**
    * @func inDetail
    * @description '입고정보 상세'
    * @param string $stockNo
    * @return array
    */
   public function inDetail(string $stockNo): array
   {
      return $this->wmsAbstract->inDetail($stockNo);
   }

   /**
    * @func outList
    * @description '출고관리 라스트'
    * @param array $params
    * @return array
    */
   public function outList(array $params): array
   {
      return $this->wmsAbstract->outList($params);
   }

   /**
    * @func outDetail
    * @description '출고정보 상세'
    * @param string $groupNo
    * @return array
    */
   public function outDetail(string $groupNo): array
   {
      return $this->wmsAbstract->outDetail($groupNo);
   }
}