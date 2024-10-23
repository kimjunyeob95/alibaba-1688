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
        Schema::create('bonaera_out_delivery_pay_log_datas', function (Blueprint $table) {
            $table->id();
            $table->string('group_no', 20)->nullable(false)->comment('배송번호');
            $table->enum('success', ["Y", "N"])->default("N")->nullable(false)->comment('응답결과 Y: 성공, N: 실패');
            $table->string('message')->nullable(false)->comment('응답 메세지');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('group_no')->references('group_no')->on('bonaera_out_base_datas')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE bonaera_out_delivery_pay_log_datas COMMENT "보내라 출고 배송 결제 통신 로그 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonaera_out_delivery_pay_log_datas');
    }
};