<?php

namespace App\Services;

use App\Abstracts\CollectAbstract;

class CollectService
{
   private CollectAbstract $collectAbstract;

   public function __construct(CollectAbstract $collectAbstract)
   {
      $this->collectAbstract = $collectAbstract;
   }

   /**
     * @func palletPrdList
     * @description 'WApp 팔레트 수집 조회'
     * @param array $params
     * @return array
   */
   public function palletPrdList(array $params): array
   {
      return $this->collectAbstract->palletPrdList($params);
   }

   /**
     * @func palletValidation
     * @description '팔레트 수집 여부 조회'
     * @param int $palletId
     * @return array
   */
   public function palletValidation(int $palletId): array
   {
      return $this->collectAbstract->palletValidation($palletId);
   }
}