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
        Schema::create('onchannel_product_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id')->unique()->nullable(false)->comment('제품ID');
            $table->string('member_id', 25)->nullable(false)->comment('공급사명');
            $table->string('prd_code', 100)->nullable(false)->comment('온채널 제품코드');
            $table->enum('regist_success', ["Y", "N"])->default("N")->nullable(false)->comment('상품등록 성공여부 Y:성공 N:실패');
            $table->string('message', 255)->nullable(false)->comment('상품등록 내용');

            $table->timestamp('registed_at')->nullable()->comment('상품등록 일자');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');
            $table->index('offer_id');
            $table->index('prd_code');
            $table->index('regist_success');
        });

        DB::statement('ALTER TABLE onchannel_product_logs COMMENT "온채널 상품 등록 로그 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('onchannel_product_logs');
    }
};
