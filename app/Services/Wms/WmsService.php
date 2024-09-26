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
    * @func getTariff
    * @description '관세율조회'
    * @param string $hsCode
    * @return array
    */
   public function getTariff(string $hsCode): array
   {
      return $this->wmsAbstract->getTariff($hsCode);
   }
}