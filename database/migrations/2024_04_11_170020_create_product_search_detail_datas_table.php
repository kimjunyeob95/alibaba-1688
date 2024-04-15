<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductSearchDetailDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_search_detail_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('search_id')->nullable(false)->comment('product_search_datas ID');
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->unsignedBigInteger('category_id')->nullable(false)->comment('카테고리ID');
            $table->text('prd_name_trans')->nullable(false)->comment('제품명_번역');
            $table->decimal('price_1688', 8, 2)->nullable(false)->default(0)->comment('1688 가격');
            $table->text('prd_image')->nullable(false)->comment('제품 이미지');
            $table->integer('sold_out')->nullable(false)->comment('판매량');
            $table->enum('is_search', ["Y", "N"])->default("N")->nullable(false)->comment('조회 성공 여부');
            $table->text('msg')->nullable(false)->comment('내용');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('search_id')->references('id')->on('product_search_datas')->onDelete('cascade');
            $table->index('search_id');
            $table->index('offer_id');
            $table->index('category_id');
            $table->index('is_search');
        });

        DB::statement('ALTER TABLE product_search_detail_datas COMMENT "1688 상품 url 검색 상세 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_search_detail_datas');
    }
}
