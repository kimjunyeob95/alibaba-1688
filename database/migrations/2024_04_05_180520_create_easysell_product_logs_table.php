<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEasysellProductLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('easysell_product_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('itemno')->default(0)->nullable(false)->comment('easysell 상품 no');
            $table->unsignedBigInteger('offer_id')->unique()->nullable(false)->comment('제품ID');
            $table->string('account', 50)->nullable(false)->comment('easysell ID');
            $table->enum('regist_success', ["Y", "N"])->default("N")->nullable(false)->comment('상품등록 성공여부 Y:성공 N:실패');
            $table->string('regist_message', 255)->nullable(false)->comment('상품등록 내용');
            $table->enum('modi_success', ["Y", "N"])->default("N")->nullable(false)->comment('상품수정 성공여부 Y:성공 N:실패');
            $table->string('modi_message', 255)->nullable(false)->comment('상품수정 내용');
            $table->timestamp('registed_at')->nullable()->comment('상품등록 일자');
            $table->timestamp('modied_at')->nullable()->comment('상품수정 일자');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');
            $table->index('itemno');
            $table->index('offer_id');
            $table->index('account');
            $table->index('regist_success');
            $table->index('modi_success');
        });

        DB::statement('ALTER TABLE easysell_product_logs COMMENT "easySell 상품 등록/수정 로그 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('easysell_product_logs');
    }
}
