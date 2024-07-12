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
        Schema::create('order_trade_datas', function (Blueprint $table) {
            $table->id();

            $table->string('order_id', 25)->nullable(false)->comment('주문ID');
            $table->unsignedBigInteger('phase')->nullable(false)->comment('단계ID');
            $table->string('pay_way_desc', 100)->nullable(false)->comment('결제 방법 설명');
            $table->boolean('express_pay')->default(false)->nullable(false)->comment('즉시 결제 여부');
            $table->timestamp('pay_time')->nullable()->comment('결제 시간');
            $table->string('pay_status_desc', 100)->nullable(false)->comment('결제 상태 설명');
            $table->string('pay_way', 50)->nullable(false)->comment('결제 방법');
            $table->boolean('card_pay')->default(false)->nullable(false)->comment('카드 결제 여부');
            $table->string('pay_status', 50)->nullable(false)->comment('결제 상태');
            $table->decimal('phas_amount', 8, 2)->default(0.0)->nullable(false)->comment('단계 금액');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')->references('order_id')->on('order_base_datas')->onDelete('cascade');

            $table->index('order_id');
            $table->index('phase');
            $table->index('pay_status');
        });

        DB::statement('ALTER TABLE order_trade_datas COMMENT "W 주문 거래조항 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_trade_datas');
    }
};
