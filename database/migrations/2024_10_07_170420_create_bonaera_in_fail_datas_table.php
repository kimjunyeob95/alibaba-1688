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
        Schema::create('bonaera_in_fail_datas', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 25)->unique()->nullable(false)->comment('주문번호');
            $table->string('hs_code', 20)->nullable(false)->comment('HS 코드');
            $table->text('msg')->nullable(false)->comment('실패 사유');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')->references('order_id')->on('order_base_datas')->onDelete('cascade');

            $table->index('order_id');
            $table->index('hs_code');
        });

        DB::statement('ALTER TABLE bonaera_in_fail_datas COMMENT "보내라 입고 통신 실패 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonaera_in_fail_datas');
    }
};