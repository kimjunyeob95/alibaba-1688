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
        Schema::create('product_w2_option_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->unsignedBigInteger('sku_id')->nullable(false)->comment('제품skuID');
            $table->text('spec_id')->nullable(false)->comment('제품specID');
            $table->enum('status', ["Y", "D", "N"])->default("Y")->nullable(false)->comment('상태값 Y: 정상(재입고), D: 단종, N: 품절');
            $table->text('option_name_en')->nullable(false)->comment('옵션명(영문)');
            $table->text('option_name_kr')->nullable(false)->comment('옵션명(국문)');
            $table->decimal('price_1688', 12, 2)->nullable(false)->default(0)->comment('W 공급가(위안)');
            $table->decimal('option_price', 12, 2)->nullable(false)->default(0)->comment('W 공급가(원화)');
            $table->integer('md_price')->nullable(false)->default(0)->comment('MD 판매가');
            $table->decimal('onch_price', 12, 2)->nullable(false)->default(0)->comment('온채널가');
            $table->decimal('cus_price', 12, 2)->nullable(false)->default(0)->comment('소비자가');
            $table->decimal('recom_cus_price', 12, 2)->nullable(false)->default(0)->comment('권장 소비자가');
            $table->unsignedInteger('amount_on_sale')->nullable(false)->default(0)->comment('재고량');
            $table->string('cargo_number', 100)->nullable(false)->comment('cargoNumber');
            $table->decimal('exchange_rate', 5, 2)->nullable(false)->default(200)->comment('적용 환율');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('offer_id')->references('offer_id')->on('product_w2_datas')->onDelete('cascade');
            $table->index('offer_id');
            $table->index('sku_id');
            $table->index('status');
            $table->index('amount_on_sale');
            $table->index('exchange_rate');
        });

        DB::statement('ALTER TABLE product_w2_option_datas COMMENT "W2 상품 옵션 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_w2_option_datas');
    }
};