<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateGenuioQueueDetailDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genuio_queue_detail_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('queue_id')->nullable(false)->comment('queue ID');
            $table->unsignedBigInteger('img_id')->nullable(false)->comment('image ID');
            $table->enum('trans_status', ["S", "Y", "N"])->nullable(false)->comment('번역 처리 여부 S: 대기, Y: 성공, N: 실패');
            $table->longText('base64')->nullable(false)->comment('이미지 base64');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('queue_id')->references('id')->on('genuio_queue_datas')->onDelete('cascade');
            $table->foreign('img_id')->references('id')->on('product_image_datas');
            $table->index('queue_id');
            $table->index('img_id');
            $table->index('trans_status');
        });

        DB::statement('ALTER TABLE genuio_queue_detail_datas COMMENT "1688, genuio 양방향 통신 상세 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genuio_queue_detail_datas');
    }
}
