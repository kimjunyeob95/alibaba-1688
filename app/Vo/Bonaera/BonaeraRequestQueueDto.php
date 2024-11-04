<?php

namespace App\Vo\Bonaera;

use App\Vo\Vo;

class BonaeraRequestQueueDto extends Vo
{
    protected string $type      = "";
    protected string $stockCode = "";
    protected string $groupCode = "";
    protected array $outCodes   = [];
    
    public function bind(mixed $data): void
    {
        $this->type      = $data["type"];
        $this->stockCode = $data["stock_code"] ?? "";
        $this->groupCode = $data["group_code"] ?? "";
        $this->outCodes  = $data["out_codes"] ?? [];
    }
}