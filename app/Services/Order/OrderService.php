<?php

namespace App\Services\Order;

use App\Abstracts\OrderAbstract;

class OrderService
{
   private OrderAbstract $orderAbstract;

   public function __construct(
      OrderAbstract $orderAbstract
   )
   {
      $this->orderAbstract = $orderAbstract;
   }

   /**
    * @func orderList
    * @description 'WApp 주문 리스트'
    * @param array $params
    * @return array
    */
   public function orderList(array $params): array
   {
      return $this->orderAbstract->orderList($params);
   }

   /**
    * @func orderWList
    * @description 'W 주문 리스트'
    * @param array $params
    * @return array
    */
   public function orderWList(array $params): array
   {
      return $this->orderAbstract->orderWList($params);
   }

   /**
    * @func orderUpdate
    * @description 'W -> WApp 주문 업데이트'
    * @param array $orderIds
    * @return array
    */
   public function orderUpdate(array $orderIds): array
   {
      return $this->orderAbstract->orderUpdate($orderIds);
   }

   /**
    * @func orderBatchUpdate
    * @description 'WApp 주문 배치 업데이트'
    * @return void
    */
   public function orderBatchUpdate(): void
   {
      $this->orderAbstract->orderBatchUpdate();
   }

   /**
    * @func orderPayLinkCreate
    * @description 'WApp 주문 결제 링크 생성'
    * @param array $params
    * @return array
   */
   public function orderPayLinkCreate(array $params): array
   {
      return $this->orderAbstract->orderPayLinkCreate($params);
   }

   /**
    * @func orderInfo
    * @description 'W 주문 조회'
    * @param string $orderId
    * @return array
    */
   public function orderInfo(string $orderId): array
   {
      return $this->orderAbstract->getWOrder($orderId);
   }

   /**
    * @func orderInfoUpdate
    * @description '주문정보 업데이트'
    * @param array $params
    * @return array
   */
   public function orderInfoUpdate(array $params): array
   {
      return $this->orderAbstract->orderInfoUpdate($params);
   }

   /**
    * @func orderWappInfo
    * @description 'WApp 주문 조회'
    * @param string $orderId
    * @return array
    */
   public function orderWappInfo(string $orderId): array
   {
      return $this->orderAbstract->orderWappInfo($orderId);
   }

   /**
    * @func orderLogisticsInfo
    * @description 'W 주문 물류 조회'
    * @param string $orderId
    * @return array
    */
   public function orderLogisticsInfo(string $orderId): array
   {
      return $this->orderAbstract->orderLogisticsInfo($orderId);
   }
}