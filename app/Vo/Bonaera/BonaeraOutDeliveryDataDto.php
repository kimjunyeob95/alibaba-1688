<?php

namespace App\Vo\Bonaera;

use App\Vo\Vo;

class BonaeraOutDeliveryDataDto extends Vo
{
    protected string $group_no       = "";
    protected string $invoice        = "";
    protected string $ctr_num        = "";
    protected string $state          = "";
    protected ?string $outday        = "";
    protected string $receiver_name  = "";
    protected string $zip_code       = "";
    protected string $addr1          = "";
    protected string $addr2          = "";
    protected string $receiver_phone = "";
    protected string $personal_type  = "";
    protected string $personal_num   = "";
    protected string $unipass_result = "";
    protected string $unipass_reason = "";
    protected string $ship_memo      = "";

    public function bind(mixed $data): void
    {
        $this->group_no       = $data["groupNo"];
        $this->invoice        = $data["invoice"];
        $this->ctr_num        = $data["ctrNum"];
        $this->state          = $data["state"];
        $this->outday         = $data["outday"];
        $this->receiver_name  = $data["receiverName"];
        $this->zip_code       = $data["zipCode"];
        $this->addr1          = $data["addr1"];
        $this->addr2          = $data["addr2"];
        $this->receiver_phone = $data["receiverPhone"];
        $this->personal_type  = $data["personalType"];
        $this->personal_num   = $data["personalNum"];
        $this->unipass_result = $data["unipassResult"];
        $this->unipass_reason = $data["unipassReason"];
        $this->ship_memo      = $data["shipMemo"];
    }
}