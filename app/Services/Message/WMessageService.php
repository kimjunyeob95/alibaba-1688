<?php

namespace App\Services\Message;

use App\Abstracts\WMessageAbstract;

class WMessageService
{
   private WMessageAbstract $wMessageAbstract;

   public function __construct(
      WMessageAbstract $wMessageAbstract,
   )
   {
      $this->wMessageAbstract = $wMessageAbstract;
   }

   /**
    * @func list
    * @description '메시지 리스트'
    * @param array $params
    * @return array
   */
   public function list(array $params): array
   {
      return $this->wMessageAbstract->list($params);
   }

   /**
    * @func detail
    * @description '메시지 상세'
    * @param int $id
    * @return array
   */
   public function detail(int $id): array
   {
      return $this->wMessageAbstract->detail($id);
   }

   /**
    * @func message
    * @description 'W1 메세지 카프카 등록'
    * @param array $params
    * @return array
   */
   public function message(array $params): array
   {
      return $this->wMessageAbstract->message($params);
   }

   /**
    * @func taobaoCallback
    * @description '타오바오 콜백'
    * @param array $params
    * @return array
   */
  public function taobaoCallback(array $params): array
  {
     return $this->wMessageAbstract->taobaoCallback($params);
  }
}