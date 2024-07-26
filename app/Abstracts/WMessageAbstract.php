<?php

namespace App\Abstracts;

abstract class WMessageAbstract
{
    protected array $returnMsg;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    /**
    * @func filterMessage
    * @description 'W1 메세지 종류 필터'
    * @param array $params
    * @return array
    */
    abstract function filterMessage(array $params): array;
}
