<?php

namespace App\Vo\Bonaera;

use App\Vo\Vo;

class BonaeraRequestQueueDto extends Vo
{
    protected string $type    = "";
    protected string $stockNo = "";
    protected string $groupNo = "";
    protected array $shNos    = [];
    
    public function bind(mixed $data): void
    {
        $this->type    = $data["type"];
        $this->stockNo = $data["stockNo"] ?? "";
        $this->groupNo = $data["groupNo"] ?? "";
        $this->shNos   = $data["shNos"] ?? [];
    }
}