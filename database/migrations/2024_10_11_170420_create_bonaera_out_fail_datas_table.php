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
        Schema::create('bonaera_out_fail_datas', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 25)->unique()->nullable(false)->comment('주문번호');
            $table->text('msg')->nullable(false)->comment('실패 사유');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')->references('order_id')->on('order_base_datas')->onDelete('cascade');

            $table->index('order_id');
        });

        DB::statement('ALTER TABLE bonaera_out_fail_datas COMMENT "보내라 출고 통신 실패 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonaera_out_fail_datas');
    }
};