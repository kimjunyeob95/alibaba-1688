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
        Schema::create('onchannel_product_detail_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('log_id')->nullable(false)->comment('onchannel_product_logs id');
            $table->enum('send_type', ["regist", "modi"])->default("regist")->nullable(false)->comment('전송 타입');
            $table->enum('is_success', ["Y", "N"])->default("N")->nullable(false)->comment('성공여부 Y:성공 N:실패');
            $table->string('message', 255)->nullable(false)->comment('상품등록 내용');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('log_id')->references('id')->on('onchannel_product_logs')->onDelete('cascade');
            $table->index('send_type');
            $table->index('is_success');
        });

        DB::statement('ALTER TABLE onchannel_product_detail_logs COMMENT "온채널 상품 등록 상세 로그 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('onchannel_product_detail_logs');
    }
};
