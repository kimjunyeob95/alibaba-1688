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
        Schema::create('order_channel_detail_datas', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('order_channel_id')->nullable(false)->comment('order_channel_datas id');
            $table->unsignedBigInteger('option_id')->nullable(false)->comment('product_option_datas id');
            $table->decimal('origin_price', 8, 2)->default(0.0)->nullable(false)->comment('product_option_datas price_1688_origin DB에 저장된 가격(위안)');
            $table->decimal('channel_price', 8, 2)->default(0.0)->nullable(false)->comment('채널에서 발주신청한 옵션 가격');
            $table->integer('quantity')->default(0)->nullable(false)->comment('수량');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_channel_id')->references('id')->on('order_channel_datas')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('product_option_datas')->onDelete('cascade');

            $table->index('order_channel_id');
            $table->index('option_id');
        });

        DB::statement('ALTER TABLE order_channel_detail_datas COMMENT "W 주문 채널별 상세 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_channel_detail_datas');
    }
};
