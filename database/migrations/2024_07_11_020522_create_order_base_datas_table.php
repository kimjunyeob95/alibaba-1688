<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_base_datas', function (Blueprint $table) {
            $table->id();

            $table->string('order_id', 25)->unique()->nullable(false)->comment('주문ID');
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->string('channel', 10)->nullable(false)->comment('채널명');
            $table->string('status', 50)->nullable(false)->comment('주문상태');
            $table->timestamp('all_delivered_time')->nullable()->comment('주문처리 완료 시간(송장입력)');
            $table->timestamp('pay_time')->nullable()->comment('결제시간');
            $table->float('discount', 8, 2)->default(0.0)->nullable(false)->comment('할인');
            $table->float('sum_product_payment', 8, 2)->default(0.0)->nullable(false)->comment('총 제품 금액');
            $table->timestamp('modify_time')->nullable()->comment('수정시간');
            $table->string('close_reason')->nullable(false)->comment('주문 취소 사유');
            $table->timestamp('complete_time')->nullable()->comment('거래종료 시간');
            $table->string('close_operate_type')->nullable(false)->comment('거래종료사유');
            $table->float('total_amount', 8, 2)->default(0.0)->nullable(false)->comment('지불 할 총액 : 제품금액 + 배송비');
            $table->string('seller_id')->nullable(false)->comment('판매자ID');
            $table->float('shipping_fee', 8, 2)->default(0.0)->nullable(false)->comment('배송비');
            $table->float('refund', 8, 2)->default(0.0)->nullable(false)->comment('환불');
            $table->float('refund_payment', 8, 2)->default(0.0)->nullable(false)->comment('환불금액');
            $table->string('refund_status')->nullable(false)->comment('환불상태');
            $table->string('phone')->nullable(false)->comment('판매자 번호');
            $table->string('email')->nullable(false)->comment('판매자 이메일');
            $table->string('im_in_platform')->nullable(false)->comment('판매자 아임인플랫폼');
            $table->string('name')->nullable(false)->comment('판매자 이름');
            $table->string('mobile')->nullable(false)->comment('판매자 핸드폰');
            $table->string('company_name')->nullable(false)->comment('판매자 회사이름');
            $table->float('coupon_fee', 8, 2)->default(0.0)->nullable(false)->comment('쿠폰수수료');
            $table->string('to_full_name')->nullable(false)->comment('수신자 전체 이름');
            $table->string('to_division_code')->nullable(false)->comment('수신자 코드');
            $table->string('to_mobile')->nullable(false)->comment('수신자 핸드폰');
            $table->string('to_post')->nullable(false)->comment('수신자 우편번호');
            $table->string('to_town_code')->nullable(false)->comment('수신자 건물번호');
            $table->string('to_area')->nullable(false)->comment('수신자 지역');
            $table->string('close_remark')->nullable(false)->comment('주문종료 추가사유');
            $table->string('trade_type')->nullable(false)->comment('거래 유형');
            $table->string('id_of_str')->nullable(false)->comment('거래ID');
            $table->boolean('step_pay_all')->default(false)->nullable(false)->comment('일회성 결제 여부');
            $table->timestamp('create_time')->nullable()->comment('생성시간');
            $table->string('business_type')->nullable(false)->comment('비즈니스 유형');
            $table->string('trade_type_desc')->nullable(false)->comment('거래 유형 설명');
            $table->string('pay_channel_list')->nullable(false)->comment('결제 채널');
            $table->string('trade_type_code')->nullable(false)->comment('거래 유형 코드');
            $table->string('pay_timeout')->nullable(false)->comment('결제 제한 시간');
            $table->string('pay_timeout_type')->nullable(false)->comment('지불 시간 초과 유형');
            $table->string('pay_channel_code_list')->nullable(false)->comment('결제 채널 코드');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');

            $table->index('order_id');
            $table->index('offer_id');
            $table->index('channel');
            $table->index('status');
        });

        DB::statement('ALTER TABLE order_base_datas COMMENT "W 주문 baseInfo 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_base_datas');
    }
};
