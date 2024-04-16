<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateGenuioImageDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genuio_image_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->unsignedBigInteger('img_id')->nullable(false)->comment('이미지ID');
            $table->string('ai_type', 25)->nullable(false)->comment('AI 알고리즘 타입');
            $table->text('img_url_ai')->nullable(false)->comment('AI 이미지');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');
            $table->foreign('img_id')->references('id')->on('product_image_datas')->onDelete('cascade');
            $table->index('offer_id');
            $table->index('img_id');
            $table->index('ai_type');
        });

        DB::statement('ALTER TABLE genuio_image_datas COMMENT "genuio AI 이미지 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genuio_image_datas');
    }
}
