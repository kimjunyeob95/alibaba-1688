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
        Schema::create('genuio_image_extend_datas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->unsignedBigInteger('img_id')->nullable(false)->comment('이미지ID');

            $table->text('cleaned_img_url')->nullable(false)->comment('흰 배경 이미지');
            $table->text('text_data')->nullable(false)->comment('텍스트 데이터');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');
            $table->foreign('img_id')->references('id')->on('product_image_datas')->onDelete('cascade');

            $table->index('offer_id');
            $table->index('img_id');
        });

        DB::statement('ALTER TABLE genuio_image_extend_datas COMMENT "genuio 흰 배경 이미지 히스토리 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genuio_image_extend_datas');
    }
};
