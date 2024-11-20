<?php

namespace App\Vo\Bonaera;

use App\Vo\Vo;

class BonaeraOutWeightDataDto extends Vo
{
    protected string $group_no         = "";
    protected int $box_cnt             = 0;
    protected float $weight            = 0;
    protected int $ship_money          = 0;
    protected int $weight_fee          = 0;
    protected int $volume_fee          = 0;
    protected int $svc_money1          = 0;
    protected int $svc_money2          = 0;
    protected int $plus_money          = 0;
    protected string $plus_money_memo  = "";
    protected int $minus_money         = 0;
    protected string $minus_money_memo = "";
    protected int $commission          = 0;
    protected int $islands             = 0;
    protected int $total_money         = 0;
    
    public function bind(mixed $data): void
    {
        $this->group_no         = $data["groupNo"];
        $this->box_cnt          = $data["boxCnt"];
        $this->weight           = $data["weight"];
        $this->ship_money       = $data["shipMoney"];
        $this->weight_fee       = $data["weightFee"];
        $this->volume_fee       = $data["volumeFee"];
        $this->svc_money1       = $data["svcMoney1"];
        $this->svc_money2       = $data["svcMoney2"];
        $this->plus_money       = $data["plusMoney"];
        $this->plus_money_memo  = $data["plusMoneyMemo"];
        $this->minus_money      = $data["minusMoney"];
        $this->minus_money_memo = $data["minusMoneyMemo"];
        $this->commission       = $data["commission"];
        $this->islands          = $data["islands"];
        $this->total_money      = $data["totalMoney"];
    }
}