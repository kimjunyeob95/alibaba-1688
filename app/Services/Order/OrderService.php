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
    * @description '주문 리스트'
    * @param array $params
    * @return array
    */
   public function orderList(array $params): array
   {
      return $this->orderAbstract->getPrdList($params);
   }
}