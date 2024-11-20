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
        Schema::create('bonaera_out_box_datas', function (Blueprint $table) {
            $table->id();

            $table->string('group_no', 20)->nullable(false)->comment('배송번호');
            $table->integer('box_cnt')->nullable(false)->comment('박스수');
            $table->float('real_weight')->nullable(false)->comment('실무게(kg)');
            $table->float('width')->nullable(false)->comment('가로(cm)');
            $table->float('length')->nullable(false)->comment('세로(cm)');
            $table->float('height')->nullable(false)->comment('높이(cm)');

            $table->timestamps();
            $table->softDeletes();

            $table->index('group_no');
        });

        DB::statement('ALTER TABLE bonaera_out_box_datas COMMENT "보내라 출고 박스 정보 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonaera_out_box_datas');
    }
};