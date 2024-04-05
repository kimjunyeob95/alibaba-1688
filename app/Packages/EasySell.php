<?php

namespace App\Packages;

use App\Abstracts\MallApiAbstract;

class EasySell extends MallApiAbstract
{
    public function __construct()
    {
    }

    public function productRegist(): array
    {
        return ["result" => "productRegist / EasySell"];
    }

    public function orderCreate(array $params): array
    {
        return ["result" => "getOrders / EasySell"];
    }
}
