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
        Schema::create('bonaera_out_delivery_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('out_base_id')->nullable(false)->comment('bonaera_out_base_datas id');
            $table->string('invoice', 20)->nullable(false)->comment('운송장번호');
            $table->string('state', 10)->default("301")->nullable(false)->comment('그룹신청서 상태');
            $table->date('outday')->nullable(true)->comment('출고일');
            $table->string('receiver_name', 20)->nullable(false)->comment('수취인');
            $table->string('zip_code', 20)->nullable(false)->comment('우편번호');
            $table->string('addr1', 100)->nullable(false)->comment('주소');
            $table->string('addr2', 100)->nullable(false)->comment('상세주소');
            $table->string('receiver_phone', 25)->nullable(false)->comment('연락처');
            $table->string('personal_type', 10)->nullable(false)->comment('개인통관번호:1 생년월일8자리:2 사업자통관번호:3');
            $table->string('personal_num', 50)->nullable(false)->comment('개인통관번호 or 생년월일8자리 or 사업자통관번호');
            $table->string('unipass_result', 10)->nullable(false)->comment('유효성검사결과 0: 불일치, 1:일치, 3:기타');
            $table->string('unipass_reason')->nullable(false)->comment('유효성검사결과가 불일치(0)인 경우 불일치 이유');
            $table->string('ship_memo')->nullable(false)->comment('배송요청사항');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('out_base_id')->references('id')->on('bonaera_out_base_datas')->onDelete('cascade');

            $table->index('out_base_id');
            $table->index('invoice');
            $table->index('state');
            $table->index('receiver_name');
            $table->index('receiver_phone');
        });

        DB::statement('ALTER TABLE bonaera_out_delivery_datas COMMENT "보내라 출고 배송정보 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonaera_out_delivery_datas');
    }
};