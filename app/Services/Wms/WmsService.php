<?php

namespace App\Services\Wms;

use App\Abstracts\WmsAbstract;
use App\Http\Request\Bonaera\BonaeraOutDeliveryUpdateRequest;

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
    * @func inFailList
    * @description '입고 실패 리스트'
    * @param array $params
    * @return array
    */
   public function inFailList(array $params): array
   {
      return $this->wmsAbstract->inFailList($params);
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
    * @func bonaeraInHscodeUpdate
    * @description '입고성공 HS code 적용'
    * @param array $ids
    * @param string $hsCode
    * @return array
    */
   public function bonaeraInHscodeUpdate(array $ids, string $hsCode): array
   {
      return $this->wmsAbstract->bonaeraInHscodeUpdate($ids, $hsCode);
   }

   /**
    * @func bonaeraInFailHscodeUpdate
    * @description '입고실패 HS code 적용'
    * @param array $ids
    * @param string $hsCode
    * @return array
    */
   public function bonaeraInFailHscodeUpdate(array $ids, string $hsCode): array
   {
      return $this->wmsAbstract->bonaeraInFailHscodeUpdate($ids, $hsCode);
   }

   /**
    * @func bonaeraInFailCreate
    * @description '입고실패 데이터 입고신청'
    * @param int $id
    * @return void
    */
   public function bonaeraInFailCreate(int $id): void
   {
      $this->wmsAbstract->bonaeraInFailCreate($id);
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
    * @func bonaeraOutCreate
    * @description '출고신청'
    * @param int $id
    * @return void
    */
   public function bonaeraOutCreate(int $id): void
   {
      $this->wmsAbstract->bonaeraOutCreate($id);
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
    * @func bonaeraOutPay
    * @description '출고 배송비 결제'
    * @param int $id
    * @return void
    */
   public function bonaeraOutPay(int $id): void
   {
      $this->wmsAbstract->bonaeraOutPay($id);
   }

   /**
    * @func bonaeraOutDeliveryUpdate
    * @description '출고 배송정보 업데이트'
    * @param BonaeraOutDeliveryUpdateRequest $request
    * @return array
    */
   public function bonaeraOutDeliveryUpdate(BonaeraOutDeliveryUpdateRequest $request): array
   {
      return $this->wmsAbstract->bonaeraOutDeliveryUpdate($request);
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
    * @func outSignList
    * @description '출고 신청관리'
    * @param array $params
    * @return array
    */
   public function outSignList(array $params): array
   {
      return $this->wmsAbstract->outSignList($params);
   }

   /**
    * @func outList
    * @description '출고 라스트'
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

   /**
    * @func RequestBonaeraTokenCreate
    * @description '토큰 생성'
    * @param string $userId
    * @return array
   */
   public function RequestBonaeraTokenCreate(string $userId): array
   {
      return $this->wmsAbstract->RequestBonaeraTokenCreate($userId);
   }

   /**
    * @func bonaeraDeliveryBundle
    * @description '묶음 배송 처리'
    * @param int $id
    * @param string $changeGroupNo
    * @return void
   */
   public function bonaeraDeliveryBundle(int $id, string $changeGroupNo): void
   {
      $this->wmsAbstract->bonaeraDeliveryBundle($id, $changeGroupNo);
   }

   /**
    * @func bonaeraOutBox
    * @description '보내라 박스 정보'
    * @param string $groupNo
    * @return array
    */
   public function bonaeraOutBox(string $groupNo): array
   {
      return $this->wmsAbstract->bonaeraOutBox($groupNo);
   }
}