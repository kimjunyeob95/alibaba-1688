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
        Schema::create('bonaera_in_product_img_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable(false)->comment('bonaera_in_product_datas id');
            $table->integer('img_number')->nullable(false)->comment('이미지 넘버');
            $table->string('img_url')->nullable(false)->comment('입고사진');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('bonaera_in_product_datas')->onDelete('cascade');

            $table->index('product_id');
            $table->index('img_number');
        });

        DB::statement('ALTER TABLE bonaera_in_product_img_datas COMMENT "보내라 입고 상품 입고 사진 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonaera_in_product_img_datas');
    }
};