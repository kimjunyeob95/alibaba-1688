<?php

namespace App\Vo\Order;

use App\Vo\Vo;
use Carbon\Carbon;

class OrderLogisticDto extends Vo
{
    /** 주문ID */
    protected string $order_id = "";
    /** 물류ID */
    protected int $logistics_id = 0;
    /** 배송 시간 */
    protected ?string $delivered_time = null;
    /** 추적 번호 */
    protected string $logistics_code = "";
    /** 물류상태 */
    protected string $status = "";
    /** 변경시간 */
    protected ?string $gmt_modified = null;
    /** 생성시간 */
    protected ?string $gmt_create = null;
    /** 물류회사번호 */
    protected string $logistics_company_no = "";
    /** 물류회사명 */
    protected string $logistics_company_name = "";
    /** 물류회사 운송장번호 */
    protected string $logistics_bill_no = "";
    /** 상품 상세 항목 ID가 여러 개인 경우 구분하여, */
    protected string $sub_item_ids = "";
   

    public function bind(mixed $data): void
    {
        $this->order_id               = $data["orderId"];
        $this->logistics_id           = $data["id"];
        $this->delivered_time         = $data["deliveredTime"] ?? null;
        $this->logistics_code         = $data["logisticsCode"] ?? "";
        $this->status                 = $data["status"] ?? "";
        $this->gmt_modified           = $data["gmtModified"] ?? null;
        $this->gmt_create             = $data["gmtCreate"] ?? null;
        $this->logistics_company_no   = $data["logisticsCompanyNo"] ?? "";
        $this->logistics_company_name = $data["logisticsCompanyName"] ?? "";
        $this->logistics_bill_no      = $data["logisticsBillNo"] ?? "";
        $this->sub_item_ids           = $data["subItemIds"] ?? "";

        $this->__changeKrUTCTime();
    }

    public function __changeKrUTCTime()
    {
        if ($this->delivered_time) {
            $this->delivered_time = Carbon::createFromFormat('YmdHisvO', $this->delivered_time)->setTimezone(config('app.timezone'));
        }
        if ($this->gmt_modified) {
            $this->gmt_modified = Carbon::createFromFormat('YmdHisvO', $this->gmt_modified)->setTimezone(config('app.timezone'));
        }
        if ($this->gmt_create) {
            $this->gmt_create = Carbon::createFromFormat('YmdHisvO', $this->gmt_create)->setTimezone(config('app.timezone'));
        }
    }
}