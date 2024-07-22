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
        Schema::create('product_channel_price_datas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->unsignedBigInteger('sku_id')->nullable(false)->comment('제품skuID');

            $table->string('current_price', 10)->nullable(false)->comment('currentPrice 값(채널가격)');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');
            $table->foreign('sku_id')->references('sku_id')->on('product_option_datas')->onDelete('cascade');

            $table->index('offer_id');
            $table->index('sku_id');
        });

        DB::statement('ALTER TABLE product_channel_price_datas COMMENT "W 상품 channelPrice 속성 정보 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_channel_price_datas');
    }
};
