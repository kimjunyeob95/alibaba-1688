<?php

namespace App\Vo\Order;

use App\Vo\Vo;
use Carbon\Carbon;

class OrderProductDto extends Vo
{
    /** 주문ID */
    protected string $order_id = "";
    /** 제품ID */
    protected int $offer_id = 0;
    /** 화물번호 */
    protected string $cargo_number = "";
    /** 품목금액 */
    protected float $item_amount = 0.0;
    /** 가격 */
    protected float $price = 0.0;
    /** 제품스냅샷 URL */
    protected string $product_snapshot_url = "";
    /** 수량 */
    protected int $quantity = 0;
    /** 환불금액 */
    protected float $refund = 0.0;
    /** skuID */
    protected int $sku_id = 0;
    /** 제품상태 */
    protected string $status = "";
    /** 환불상태 */
    protected string $refund_status = "";
    /** 하위 항목 ID */
    protected int $sub_item_id = 0;
    /** 단위 */
    protected string $unit = "";
    /** 입장할인 */
    protected float $entry_discount = 0.0;
    /** specID */
    protected string $spec_id = "";
    /** 수량개수 */
    protected int $quantity_factor = 0;
    /** 상태Str */
    protected string $status_str = "";
    /** 종료사유 */
    protected string $close_reason = "";
    /** 물류상태 */
    protected int $logistics_status = 0;
    /** gmt생성일 */
    protected ?string $gmt_create = null;
    /** gmt수정일 */
    protected ?string $gmt_modified = null;
    /** gmt완료일 */
    protected ?string $gmt_completed = null;

    public function bind(mixed $data): void
    {
        $this->order_id             = $data["orderId"];
        $this->offer_id             = $data["productID"];
        $this->cargo_number         = $data["cargoNumber"] ?? "";
        $this->item_amount          = $data["itemAmount"] ?? 0.0;
        $this->price                = $data["price"] ?? 0.0;
        $this->product_snapshot_url = $data["productSnapshotUrl"] ?? "";
        $this->quantity             = $data["quantity"] ?? 0;
        $this->refund               = $data["refund"] ?? 0.0;
        $this->sku_id               = $data["skuID"] ?? 0;
        $this->status               = $data["status"] ?? "";
        $this->refund_status        = $data["refundStatus"] ?? "";
        $this->sub_item_id          = $data["subItemID"] ?? 0;
        $this->unit                 = $data["unit"] ?? "";
        $this->entry_discount       = $data["entryDiscount"] ?? 0.0;
        $this->spec_id              = $data["specId"] ?? "";
        $this->quantity_factor      = $data["quantityFactor"] ?? 0;
        $this->status_str           = $data["statusStr"] ?? "";
        $this->close_reason         = $data["closeReason"] ?? "";
        $this->logistics_status     = $data["logisticsStatus"];
        $this->gmt_create           = $data["gmtCreate"] ?? null;
        $this->gmt_modified         = $data["gmtModified"] ?? null;
        $this->gmt_completed        = $data["gmtCompleted"] ?? null;

        $this->__changeUTCTime();
    }

    public function __changeUTCTime()
    {
        if ($this->gmt_create) {
            $this->gmt_create = Carbon::createFromFormat('YmdHisvO', $this->gmt_create);
        }
        if ($this->gmt_modified) {
            $this->gmt_modified = Carbon::createFromFormat('YmdHisvO', $this->gmt_modified);
        }
        if ($this->gmt_completed) {
            $this->gmt_completed = Carbon::createFromFormat('YmdHisvO', $this->gmt_completed);
        }
    }
}