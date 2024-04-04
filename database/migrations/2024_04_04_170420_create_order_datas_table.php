<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOrderDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable(false)->unique()->comment('주문ID');
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->text('spec_id')->nullable(false)->comment('제품specID');
            $table->text('receive_name')->nullable(false)->comment('수취인 이름');
            $table->text('receive_tell')->nullable(false)->comment('수취인 전화번호');
            $table->text('receive_phone')->nullable(false)->comment('수취인 휴대폰번호');
            $table->integer('quantity')->default(1)->nullable(false)->comment('주문 수량');
            $table->decimal('order_ammount', 8, 2)->nullable(false)->default(0)->comment('주문금액');
            $table->decimal('post_fee', 8, 2)->nullable(false)->default(0)->comment('우편요금');
            $table->text('message')->nullable(false)->comment('주문 설명');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('restrict');
            $table->index('order_id');
            $table->index('offer_id');
        });

        DB::statement('ALTER TABLE order_datas COMMENT "1688 주문 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_datas');
    }
}
