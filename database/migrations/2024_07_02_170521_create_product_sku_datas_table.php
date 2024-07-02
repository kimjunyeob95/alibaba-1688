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
        Schema::create('product_sku_datas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->unsignedBigInteger('sku_id')->nullable(false)->comment('제품skuID');

            $table->decimal('price', 8, 2)->nullable(false)->comment('W 판매가');
            $table->decimal('jxhy_price', 8, 2)->nullable(false)->comment('W 제조사 공급가');
            $table->decimal('pf_jxhy_price', 8, 2)->nullable(false)->comment('W 제조사 확정 공급가');
            $table->decimal('consign_price', 8, 2)->nullable(false)->comment('W 판매자가 제공하는 공급가');
            $table->decimal('promotion_price', 8, 2)->nullable(false)->comment('W 프로모션 가격');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');
            $table->foreign('sku_id')->references('sku_id')->on('product_option_datas')->onDelete('cascade');

            $table->index('offer_id');
            $table->index('sku_id');
        });

        DB::statement('ALTER TABLE product_sku_datas COMMENT "W 상품 추가 옵션 정보 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_sku_datas');
    }
};
