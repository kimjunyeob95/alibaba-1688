<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductImageDetailDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_image_detail_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->text('img_url_origin')->nullable(false)->comment('제품 이미지 원본');
            $table->unsignedInteger('width')->nullable(false)->comment('이미지 가로');
            $table->unsignedInteger('height')->nullable(false)->comment('이미지 세로');
            $table->unsignedInteger('byte')->nullable(false)->comment('이미지 바이트');
            $table->string('mime', 20)->nullable(false)->comment('이미지 mime');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');
            $table->index('offer_id');
        });

        DB::statement('ALTER TABLE product_image_detail_datas COMMENT "1688 상품 이미지 상세 정보 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_image_detail_datas');
    }
}
