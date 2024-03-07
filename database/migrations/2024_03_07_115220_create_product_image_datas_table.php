<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductImageDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_image_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->text('img_url')->nullable(false)->comment('제품 이미지');

            $table->timestamps();
            $table->softDeletes();

            $table->index('offer_id');
        });

        DB::statement('ALTER TABLE product_image_datas COMMENT "1688 상품 이미지 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_image_datas');
    }
}
