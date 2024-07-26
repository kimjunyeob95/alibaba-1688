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
    * @func filterMessage
    * @description 'W1 메세지 종류 필터'
    * @param array $params
    * @return array
   */
   public function filterMessage(array $params): array
   {
      return $this->wMessageAbstract->filterMessage($params);
   }
}