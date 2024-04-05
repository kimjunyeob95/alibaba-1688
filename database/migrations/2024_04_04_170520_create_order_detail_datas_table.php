<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_detail_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable(false)->unique()->comment('주문ID');
            $table->longText('response_json')->nullable(false)->comment('주문 상세 전문');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')->references('order_id')->on('order_datas')->onDelete('cascade');

        });

        DB::statement('ALTER TABLE order_detail_datas COMMENT "1688 주문 상세 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_detail_datas');
    }
}
