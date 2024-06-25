<?php

namespace App\Abstracts;

use App\Models\ProductCollectPalletData;
use Exception;

abstract class CollectAbstract
{
    protected array $returnMsg;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    public function palletValidation(int $palletId): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $result = [
                "validate" => false,
            ];
            $cnt = ProductCollectPalletData::where("pallet_id", $palletId)->count();

            if( $cnt > 0 ){
                $result["validate"] = true;
            }

            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
