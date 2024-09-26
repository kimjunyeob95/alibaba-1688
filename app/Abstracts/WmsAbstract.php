<?php

namespace App\Abstracts;

abstract class WmsAbstract
{
    protected array $returnMsg;
    protected string $unipass_token;

    public function __construct()
    {
        $this->returnMsg     = helpers_fail_message();
        $this->unipass_token = env("UNIPASS_TOKEN", "g230z224g099q163r080k040u0");
    }

    /**
    * @func hsCodeList
    * @description 'HS code 라스트'
    * @param array $params
    * @return array
    */
    abstract function hsCodeList(array $params): array;

    /**
    * @func getTariff
    * @description '관세율조회'
    * @param string $hsCode
    * @return array
    */
    abstract function getTariff(string $hsCode): array;
}
