<?php

namespace App\Services\Message;

use App\Abstracts\OrderAbstract;
use App\Abstracts\WMessageAbstract;
use Exception;

class MessageW1 extends WMessageAbstract
{
    private OrderAbstract $orderW1;

    public function __construct(
        OrderAbstract $orderW1
    )
    {
        parent::__construct();
        $this->orderW1 = $orderW1;
    }

    /**
    * @func filterMessage
    * @description 'W1 메세지 종류 필터'
    * @param array $params
    * @return array
    */
    public function filterMessage(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            debug_log(json_encode($params, JSON_UNESCAPED_UNICODE), "1688/message", "message");
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
        
    }
}