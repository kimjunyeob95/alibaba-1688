<?php

namespace App\Packages;

use App\Abstracts\MallApiAbstract;
use Exception;

class EasySell extends MallApiAbstract
{
    public function __construct()
    {
        parent::__construct();
    }

    public function productRegist(): array
    {
        return ["result" => "productRegist / EasySell"];
    }

    public function orderInfo(int $orderId): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $returnMsg = helpers_success_message(["orderId" => $orderId]);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }
        
        return $returnMsg;
    }

    public function orderCreate(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $returnMsg = helpers_success_message($params);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
