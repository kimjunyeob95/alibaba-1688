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
        Schema::create('bonaera_out_base_datas', function (Blueprint $table) {
            $table->id();
            $table->string('sh_no', 20)->unique()->nullable(false)->comment('출고 신청 번호');
            $table->string('group_no', 20)->nullable(false)->comment('배송번호');
            $table->string('stock_no', 20)->nullable(false)->comment('입고번호');
            $table->string('order_id', 25)->nullable(false)->comment('주문번호');
            $table->string('channel_order_id', 25)->nullable(false)->comment('채널 주문번호');

            $table->timestamp('out_ordered_at')->nullable()->comment('출고지시일');
            $table->timestamp('out_completed_at')->nullable()->comment('출고완료일');

            $table->text('memo')->nullable(false)->comment('메모');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('stock_no')->references('stock_no')->on('bonaera_in_base_datas')->onDelete('cascade');
            $table->foreign('order_id')->references('order_id')->on('order_base_datas')->onDelete('cascade');
            $table->foreign('channel_order_id')->references('channel_order_id')->on('order_channel_datas')->onDelete('cascade');

            $table->index('sh_no');
            $table->index('group_no');
            $table->index('stock_no');
            $table->index('order_id');
            $table->index('channel_order_id');
        });

        DB::statement('ALTER TABLE bonaera_out_base_datas COMMENT "보내라 출고 기본정보 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonaera_out_base_datas');
    }
};