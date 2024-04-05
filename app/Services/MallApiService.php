<?php

namespace App\Services;

use App\Abstracts\MallApiAbstract;

class MallApiService
{
    protected array $returnMsg;
    private MallApiAbstract $mallApiAbstract;

    public function __construct(MallApiAbstract $mallApiAbstract)
    {
        $this->mallApiAbstract = $mallApiAbstract;
        $this->returnMsg       = helpers_fail_message();
    }

    public function productRegist(): array
    {
        return $this->mallApiAbstract->productRegist();
    }

    public function orderCreate(array $params): array
    {
        return $this->mallApiAbstract->orderCreate($params);
    }
}