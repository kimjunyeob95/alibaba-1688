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
        Schema::create('bonaera_in_base_datas', function (Blueprint $table) {
            $table->id();
            $table->string('stock_no', 20)->unique()->nullable(false)->comment('입고번호');
            $table->string('order_id', 25)->unique()->nullable(false)->comment('주문번호');
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('상품번호');

            $table->timestamp('completed_at')->nullable()->comment('입고완료일자');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')->references('order_id')->on('order_base_datas')->onDelete('cascade');
            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');

            $table->index('stock_no');
            $table->index('order_id');
            $table->index('offer_id');

        });

        DB::statement('ALTER TABLE bonaera_in_base_datas COMMENT "보내라 입고 기본정보 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonaera_in_base_datas');
    }
};