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
        Schema::create('bonaera_out_product_datas', function (Blueprint $table) {
            $table->id();
            $table->string('sh_no', 20)->nullable(false)->comment('출고 신청 번호');
            $table->string('channel_order_id', 25)->nullable(false)->comment('채널 주문번호');
            $table->unsignedBigInteger('option_id')->nullable(false)->comment('옵션 ID');
            $table->string('it_code', 20)->nullable(false)->comment('재고번호');
            $table->integer('quantity')->default(0)->nullable(false)->comment('신청수량');
            $table->integer('shipped_qty')->default(0)->nullable(false)->comment('출고수량');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sh_no')->references('sh_no')->on('bonaera_out_base_datas')->onDelete('cascade');
            $table->foreign('channel_order_id')->references('channel_order_id')->on('order_channel_datas')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('product_option_datas')->onDelete('cascade');

            $table->index('sh_no');
            $table->index('channel_order_id');
            $table->index('option_id');
            $table->index('it_code');
        });

        DB::statement('ALTER TABLE bonaera_out_product_datas COMMENT "보내라 출고 상품정보 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonaera_out_product_datas');
    }
};