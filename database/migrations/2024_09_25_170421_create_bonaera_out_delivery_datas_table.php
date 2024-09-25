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
            $table->string('group_no', 20)->nullable(false)->comment('그룹번호');
            $table->string('type', 20)->nullable(false)->comment('배송타입');
            

            $table->timestamps();
            $table->softDeletes();

            

            $table->index('group_no');
            $table->index('type');
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