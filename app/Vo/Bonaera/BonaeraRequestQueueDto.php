<?php

namespace App\Vo\Bonaera;

use App\Vo\Vo;

class BonaeraRequestQueueDto extends Vo
{
    protected string $type          = "";
    protected string $stockNo       = "";
    protected string $groupNo       = "";
    protected string $shNo          = "";
    protected string $originGroupNo = "";
    protected string $changeGroupNo = "";
    protected array $shNos          = [];
    
    public function bind(mixed $data): void
    {
        $this->type          = $data["type"];
        $this->stockNo       = $data["stockNo"] ?? "";
        $this->groupNo       = $data["groupNo"] ?? "";
        $this->shNo          = $data["shNo"] ?? "";
        $this->originGroupNo = $data["originGroupNo"] ?? "";
        $this->changeGroupNo = $data["changeGroupNo"] ?? "";
        $this->shNos         = $data["shNos"] ?? [];
    }
}