<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductExtendDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_extend_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');

            $table->decimal('send_default_price', 8, 2)->nullable(false)->comment('기본 배송비');
            $table->decimal('send_jeju_price', 8, 2)->nullable(false)->comment('제주도 배송비');
            $table->decimal('send_etc_price', 8, 2)->nullable(false)->comment('도서산간지역 배송비');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');
            $table->index('offer_id');
        });

        DB::statement('ALTER TABLE product_extend_datas COMMENT "1688 상품 확장 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_extend_datas');
    }
}
