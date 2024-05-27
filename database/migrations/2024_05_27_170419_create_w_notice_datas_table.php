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
        Schema::create('w_notice_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('attribute_id')->nullable(false)->comment('고시ID');
            $table->enum('lang', ["cn", "kr", "en"])->default("kr")->nullable(false)->comment('cn: 중문, kr: 국문, en: 영문');
            $table->text('attribute_name')->nullable(false)->comment('고시이름');
            $table->text('attribute_value')->nullable(false)->comment('고시값');

            $table->timestamps();
            $table->softDeletes();
            
            $table->index('attribute_id');
        });

        DB::statement('ALTER TABLE w_notice_datas COMMENT "1688 정고보시 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('w_notice_datas');
    }
};