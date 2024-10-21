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
        Schema::create('bonaera_out_delivery_extra_datas', function (Blueprint $table) {
            $table->id();
            $table->string('group_no', 20)->nullable(false)->comment('배송번호');
            $table->string('extra_name')->nullable(false)->comment('이름');
            $table->decimal('extra_money', 8, 2)->nullable(false)->default(0)->comment('가격(KRW)');
            $table->integer('extra_cnt')->nullable(false)->comment('개수');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('group_no')->references('group_no')->on('bonaera_out_base_datas')->onDelete('cascade');
        });

        DB::statement('ALTER TABLE bonaera_out_delivery_extra_datas COMMENT "보내라 출고 배송 부가서비스 정보 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonaera_out_delivery_extra_datas');
    }
};