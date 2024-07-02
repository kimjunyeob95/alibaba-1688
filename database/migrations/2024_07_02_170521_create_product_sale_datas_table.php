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
        Schema::create('product_sale_datas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->unsignedInteger('amount_on_sale')->nullable(false)->comment('전체 판매 가능 수량');
            $table->unsignedInteger('start_quantity')->nullable(false)->comment('시작 수량');
            
            $table->unsignedTinyInteger('quote_type')->nullable(false)->comment('판매 타입');
            $table->decimal('price', 8, 2)->nullable(false)->comment('가격');
            $table->decimal('consign_price', 8, 2)->nullable(false)->comment('W 판매자가 제공받는 공급가');
            $table->decimal('jxhy_price', 8, 2)->nullable(false)->comment('W 제조사 공급가');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');

            $table->index('offer_id');
            $table->index('amount_on_sale');
            $table->index('start_quantity');
            $table->index('quote_type');
        });

        DB::statement('ALTER TABLE product_sale_datas COMMENT "W 상품 추가 판매 정보 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_sale_datas');
    }
};
