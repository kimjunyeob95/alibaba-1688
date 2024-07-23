<?php

namespace App\Traits;

use App\Abstracts\ProductAbstract;
use Exception;

trait MallProductTrait
{
    protected array $returnMsg;
    protected string $channel;
    protected ProductAbstract $productW1;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    public function initProductTrait(string $channel, ProductAbstract $productW1): void
    {
        $this->channel   = $channel;
        $this->productW1 = $productW1;
    }

    /**
     * @func productWappRegistTrait
     * @description 'WApp에 상품등록'
     * @param int $offerId
     * @return array
    */
    public function productWappRegistTrait(int $offerId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $result = $this->productW1->collectProductNotLog($offerId);
            if( $result["isSuccess"] === true ){
                $returnMsg = helpers_success_message();   
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
