<?php

namespace App\Traits\WApp;

use Exception;

trait CollectProductTrait
{
    protected string $accessToken;
    protected array $returnMsg;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    public function initCollectProductTrait(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @func getWAppDto
     * @description 'WApp Dto 생성'
     * @param array $detailProduct
     * @return array
    */
    public function getWAppDto(array $detailProduct): array
    {
        $returnMsg = $this->returnMsg;
        try {
            
            dd($detailProduct);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
