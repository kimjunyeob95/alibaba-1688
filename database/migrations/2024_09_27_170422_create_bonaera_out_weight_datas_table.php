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
        Schema::create('bonaera_out_weight_datas', function (Blueprint $table) {
            $table->id();

            $table->string('gr_code', 20)->nullable(false)->comment('그룹번호');
            $table->integer('box_cnt')->nullable(false)->comment('박스수');
            $table->float('real_weight')->nullable(false)->comment('실무게(kg)');
            $table->float('width')->nullable(false)->comment('가로(cm)');
            $table->float('length')->nullable(false)->comment('세로(cm)');
            $table->float('height')->nullable(false)->comment('높이(cm)');
            $table->float('weight')->nullable(false)->comment('적용무게(kg)');
            $table->integer('ship_money')->nullable(false)->comment('기본배송비(KRW)');
            $table->integer('weight_fee')->nullable(false)->comment('무게할증료(KRW)');
            $table->integer('volume_fee')->nullable(false)->comment('부피할증료(KRW)');
            $table->integer('svc_money1')->nullable(false)->comment('부가서비스[입고]합계(KRW)');
            $table->integer('svc_money2')->nullable(false)->comment('부가서비스[출고]합계(KRW)');
            $table->integer('plus_money')->nullable(false)->comment('추가요금(KRW)');
            $table->string('plus_money_memo')->nullable(false)->comment('추가요금메모');
            $table->integer('minus_money')->nullable(false)->comment('추가할인(KRW)');
            $table->string('minus_money_memo')->nullable(false)->comment('추가할인메모');
            $table->integer('commission')->nullable(false)->comment('수수료(KRW)');
            $table->integer('islands')->nullable(false)->comment('도서산간(KRW)');
            $table->integer('total_money')->nullable(false)->comment('총배송금액(KRW)');

            $table->timestamps();
            $table->softDeletes();

            $table->index('gr_code');
        });

        DB::statement('ALTER TABLE bonaera_out_weight_datas COMMENT "보내라 출고 무게정보 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonaera_out_weight_datas');
    }
};