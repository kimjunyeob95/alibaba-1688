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
        Schema::create('product_add_datas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->unsignedBigInteger('top_category_id')->nullable(false)->comment('1차 카테고리 ID');
            $table->unsignedBigInteger('second_category_id')->nullable(false)->comment('2차 카테고리 ID');
            $table->unsignedBigInteger('third_category_id')->nullable(false)->comment('3차 카테고리 ID');
            $table->text('main_video')->nullable(false)->comment('메인 비디오 url');
            $table->text('detail_video')->nullable(false)->comment('상세 비디오 url');
            $table->unsignedInteger('min_order_quantity')->nullable(false)->comment('주문 기준 최소 구매 수량');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');

            $table->index('offer_id');
            $table->index('top_category_id');
            $table->index('second_category_id');
            $table->index('third_category_id');
            $table->index('min_order_quantity');
        });

        DB::statement('ALTER TABLE product_add_datas COMMENT "W 상품 추가 기본 정보 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_add_datas');
    }
};
