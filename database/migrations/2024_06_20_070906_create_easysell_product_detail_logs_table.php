<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEasysellProductDetailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('easysell_product_detail_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('log_id')->nullable(false)->comment('easysell_product_logs id');
            $table->enum('send_type', ["regist", "modi"])->default("regist")->nullable(false)->comment('전송 타입');
            $table->enum('is_success', ["Y", "N"])->default("N")->nullable(false)->comment('성공여부 Y:성공 N:실패');
            $table->string('message', 255)->nullable(false)->comment('상품등록 내용');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('log_id')->references('id')->on('easysell_product_logs')->onDelete('cascade');
            $table->index('send_type');
            $table->index('is_success');
        });

        DB::statement('ALTER TABLE easysell_product_detail_logs COMMENT "이지셀 상품 등록 상세 로그 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('easysell_product_detail_logs');
    }
}
