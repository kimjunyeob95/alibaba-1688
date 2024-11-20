<?php

namespace App\Vo\Bonaera;

use App\Vo\Vo;

class BonaeraOutBoxDataDto extends Vo
{
    protected string $group_no         = "";
    protected int $box_cnt             = 1;
    protected float $real_weight       = 0;
    protected float $width             = 0;
    protected float $length            = 0;
    protected float $height            = 0;

    public function bind(mixed $data): void
    {
        $this->group_no         = $data["groupNo"];
        $this->box_cnt          = $data["boxCnt"];
        $this->real_weight      = $data["realWeight"];
        $this->width            = $data["width"];
        $this->length           = $data["length"];
        $this->height           = $data["height"];
    }
}