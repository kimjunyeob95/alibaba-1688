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
        Schema::create('product_w2_image_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->enum('img_type', ["main", "sub", "desc"])->nullable(false)->comment('이미지 타입 main: 제품 메인 이미지, sub: 제품 서브 이미지, desc: 제품 상세 이미지');
            $table->enum('is_except', ["Y", "N"])->nullable(false)->default("N")->comment('수집 제외 여부');
            $table->text('img_url_origin')->nullable(false)->comment('제품 이미지 원본');
            $table->text('img_url_trans')->nullable(false)->comment('제품 이미지 번역');
            $table->timestamp('trans_dated_at')->nullable()->comment('번역 일자');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('offer_id')->references('offer_id')->on('product_w2_datas')->onDelete('cascade');
            $table->index('offer_id');
            $table->index('img_type');
            $table->index('is_except');
        });

        DB::statement('ALTER TABLE product_w2_image_datas COMMENT "W2 상품 이미지 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_w2_image_datas');
    }
};