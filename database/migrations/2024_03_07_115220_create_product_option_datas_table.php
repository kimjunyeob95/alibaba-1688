<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductOptionDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_option_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->unsignedInteger('sku_id')->nullable(false)->comment('제품skuID');
            $table->unsignedInteger('spec_id')->nullable(false)->comment('제품specID');
            $table->enum('status', [1, 2, 3])->default(1)->nullable(false)->comment('상태값 1: 정상(재입고), 2: 단종, 3: 품절');
            $table->text('option_name')->nullable(false)->comment('옵션명');
            $table->text('option_name_trans')->nullable(false)->comment('옵션명(번역)');
            $table->decimal('price_1688', 8, 3)->nullable(false)->default(0)->comment('가격(도매)');
            $table->decimal('option_price', 8, 3)->nullable(false)->default(0)->comment('옵션가격');
            $table->decimal('onch_price', 8, 3)->nullable(false)->default(0)->comment('온채널가');
            $table->decimal('cus_price', 8, 3)->nullable(false)->default(0)->comment('소비자가');
            $table->decimal('recom_cus_price', 8, 3)->nullable(false)->default(0)->comment('권장 소비자가');
            $table->decimal('consign_price', 8, 3)->nullable(false)->default(0)->comment('가격(드랍쉬핑)');
            $table->unsignedInteger('amount_on_sale')->nullable(false)->default(0)->comment('재고량');
            $table->text('sku_image_url')->nullable(false)->comment('옵션이미지');
            $table->string('cargo_number', 100)->nullable(false)->comment('cargoNumber');

            $table->timestamps();
            $table->softDeletes();

            $table->index('offer_id');
            $table->index('sku_id');
            $table->index('spec_id');
            $table->index('status');
            $table->index('amount_on_sale');
        });

        DB::statement('ALTER TABLE product_option_datas COMMENT "1688 상품 옵션 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_option_datas');
    }
}
