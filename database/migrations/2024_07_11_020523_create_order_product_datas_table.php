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
        Schema::create('order_product_datas', function (Blueprint $table) {
            $table->id();

            $table->string('order_id', 25)->nullable(false)->comment('주문ID');
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->string('cargo_number', 100)->nullable(false)->comment('화물 번호');
            $table->decimal('item_amount', 8, 2)->default(0.0)->nullable(false)->comment('항목 금액');
            $table->decimal('price', 8, 2)->default(0.0)->nullable(false)->comment('가격');
            $table->string('product_snapshot_url')->nullable(false)->comment('제품 스냅샷 URL');
            $table->integer('quantity')->default(0)->nullable(false)->comment('수량');
            $table->decimal('refund', 8, 2)->default(0.0)->nullable(false)->comment('환불 금액');
            $table->unsignedBigInteger('sku_id')->nullable(false)->comment('SKU ID');
            $table->string('status', 50)->nullable(false)->comment('상태');
            $table->unsignedBigInteger('sub_item_id')->nullable(false)->comment('하위 항목 ID');
            $table->string('unit', 50)->nullable(false)->comment('단위');
            $table->decimal('entry_discount', 8, 2)->default(0.0)->nullable(false)->comment('입력 할인');
            $table->text('spec_id')->nullable(false)->comment('제품specID');
            $table->integer('quantity_factor')->default(0)->nullable(false)->comment('수량 요인');
            $table->string('status_str', 100)->nullable(false)->comment('상태 문자열');
            $table->string('close_reason', 255)->nullable(false)->comment('닫기 이유');
            $table->integer('logistics_status')->nullable(false)->comment('물류 상태');
            $table->timestamp('gmt_create')->nullable()->comment('생성 시간');
            $table->timestamp('gmt_modified')->nullable()->comment('수정 시간');
            $table->timestamp('gmt_completed')->nullable()->comment('완료 시간');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')->references('order_id')->on('order_base_datas')->onDelete('cascade');
            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');
            $table->foreign('sku_id')->references('sku_id')->on('product_option_datas')->onDelete('cascade');

            $table->index('order_id');
            $table->index('offer_id');
            $table->index('sku_id');
            $table->index('quantity');
        });

        DB::statement('ALTER TABLE order_product_datas COMMENT "W 주문 상품 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_product_datas');
    }
};
