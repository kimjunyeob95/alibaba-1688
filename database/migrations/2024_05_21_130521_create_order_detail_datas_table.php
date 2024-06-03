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
        Schema::create('order_detail_datas', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 25)->nullable(false)->comment('주문ID');
            $table->unsignedBigInteger('option_id')->nullable(false)->comment('옵션 ID');
            $table->integer('quantity')->default(1)->nullable(false)->comment('옵션 수량');
            $table->decimal('origin_option_price', 8, 2)->default(0)->nullable(false)->comment('기존 옵션 금액');
            $table->decimal('channel_option_price', 8, 2)->default(0)->nullable(false)->comment('채널 옵션 금액');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')->references('order_id')->on('order_datas')->onDelete('restrict');
            $table->foreign('option_id')->references('id')->on('product_option_datas')->onDelete('restrict');

            $table->index('order_id');
            $table->index('option_id');
            $table->index('quantity');
            $table->index('origin_option_price');
            $table->index('channel_option_price');

        });

        DB::statement('ALTER TABLE order_detail_datas COMMENT "WApp 주문 상세 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_detail_datas');
    }
};
