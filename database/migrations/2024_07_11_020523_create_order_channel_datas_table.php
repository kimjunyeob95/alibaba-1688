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
        Schema::create('order_channel_datas', function (Blueprint $table) {
            $table->id();
            
            $table->string('order_id', 25)->nullable(false)->comment('주문ID');
            $table->string('channel_order_id', 25)->nullable(false)->comment('채널 주문ID');
            $table->decimal('total_price', 8, 2)->default(0)->nullable(false)->comment('product_option_datas price_1688_origin 총 금액');
            $table->decimal('total_channel_price', 8, 2)->default(0)->nullable(false)->comment('채널에서 전송한 총 금액');
            $table->integer('total_quantity')->default(0)->nullable(false)->comment('총 주문수량');
            $table->string('buyer_name', 25)->nullable(false)->comment('구매자명');
            $table->string('buyer_clearance_number', 30)->nullable(false)->comment('구매자 개인통관번호');
            $table->string('buyer_number', 20)->nullable(false)->comment('구매자 전화번호');
            $table->string('buyer_phone', 20)->nullable(false)->comment('구매자 핸드폰번호');
            $table->string('buyer_zipcode', 10)->nullable(false)->comment('구매자 우편번호');
            $table->string('buyer_address', 255)->nullable(false)->comment('구매자 주소');
            $table->string('buyer_memo', 255)->nullable(false)->comment('배송 요청사항');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')->references('order_id')->on('order_base_datas')->onDelete('cascade');

            $table->index('order_id');
            $table->index('channel_order_id');
            $table->index('buyer_name');
            $table->index('buyer_clearance_number');
        });

        DB::statement('ALTER TABLE order_channel_datas COMMENT "W 주문 채널별 기본 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_channel_datas');
    }
};
