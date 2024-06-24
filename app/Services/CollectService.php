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