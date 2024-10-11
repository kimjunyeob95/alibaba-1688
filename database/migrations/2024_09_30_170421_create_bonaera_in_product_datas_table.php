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
        Schema::create('bonaera_in_product_datas', function (Blueprint $table) {
            $table->id();
            $table->string('stock_no', 20)->nullable(false)->comment('입고번호');
            $table->string('order_id', 25)->nullable(false)->comment('주문번호');
            $table->unsignedBigInteger('option_id')->nullable(false)->comment('옵션 ID');
            $table->integer('quantity')->default(0)->nullable(false)->comment('수량');
            $table->string('product_snapshot_url')->nullable(false)->comment('제품 스냅샷 URL');
            $table->string('hs_code', 20)->nullable(false)->comment('HS 코드');
            $table->string('it_code', 20)->nullable(false)->comment('재고번호');
            $table->string('status', 20)->default("1001")->nullable(false)->comment('입고상태');
            $table->integer('received_qty')->default(0)->nullable(false)->comment('입고수량');
            $table->integer('discarded_qty')->default(0)->nullable(false)->comment('폐기수량');
            $table->integer('refunded_qty')->default(0)->nullable(false)->comment('환불수량');
            $table->integer('shipped_qty')->default(0)->nullable(false)->comment('출고수량');
            $table->enum('lack_status', ["Y", "N"])->default("N")->nullable(false)->comment('재고상태 Y: 재고O, N: 재고X');
            $table->integer('stock_qty')->default(0)->nullable(false)->comment('재고수량');
            $table->text('memo')->nullable(false)->comment('메모');
            $table->timestamp('in_comming_at')->nullable()->comment('입고일');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('stock_no')->references('stock_no')->on('bonaera_in_base_datas')->onDelete('cascade');
            $table->foreign('order_id')->references('order_id')->on('order_base_datas')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('product_option_datas')->onDelete('cascade');

            $table->index('stock_no');
            $table->index('order_id');
            $table->index('option_id');
            $table->index('hs_code');
            $table->index('it_code');
            $table->index('status');
            $table->index('lack_status');
        });

        DB::statement('ALTER TABLE bonaera_in_product_datas COMMENT "보내라 입고 상품정보 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonaera_in_product_datas');
    }
};