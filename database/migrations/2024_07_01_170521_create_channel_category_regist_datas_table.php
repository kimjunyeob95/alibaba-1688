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
        Schema::create('channel_category_regist_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable(false)->comment('카테고리 ID');
            $table->string('channel', 10)->nullable(false)->comment('전송 채널');
            $table->string('send_type', 10)->nullable(false)->comment('전송 채널 타입');
            $table->enum('is_regist', ["Y", "N"])->default("N")->nullable(false)->comment('전송여부 Y:전송 N:전송제외');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('category_id')->on('category_trees')->onDelete('cascade');

            $table->index('category_id');
            $table->index('channel');
            $table->index('send_type');
            $table->index('is_regist');
        });

        DB::statement('ALTER TABLE channel_category_regist_datas COMMENT "채널별 전송 카테고리 등록 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channel_category_regist_datas');
    }
};
