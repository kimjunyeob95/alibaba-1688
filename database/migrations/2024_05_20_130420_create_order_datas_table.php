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
        Schema::create('order_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable(false)->unique()->comment('주문ID');
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->string('channel', 20)->nullable(false)->comment('채널');
            $table->text('buyer_name')->nullable(false)->comment('구매자명');
            $table->text('buyer_clearance_number')->nullable(false)->comment('구매자 개인통관번호');
            $table->text('buyer_number')->nullable(false)->comment('구매자 전화번호');
            $table->text('buyer_phone')->nullable(false)->comment('구매자 핸드폰번호');
            $table->text('buyer_zipcode')->nullable(false)->comment('구매자 우편번호');
            $table->text('buyer_address')->nullable(false)->comment('구매자 주소');
            $table->text('buyer_memo')->nullable(false)->comment('배송 요청사항');
            $table->integer('total_quantity')->default(1)->nullable(false)->comment('주문 총 수량');
            $table->decimal('total_price', 8, 2)->default(0)->nullable(false)->comment('주문 총 금액');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('restrict');
            $table->index('order_id');
            $table->index('offer_id');
            $table->index('channel');
            $table->index('total_quantity');
            $table->index('total_price');
        });

        DB::statement('ALTER TABLE order_datas COMMENT "WApp 주문 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_datas');
    }
};
