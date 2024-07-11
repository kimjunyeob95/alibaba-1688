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
        Schema::create('order_logistics_datas', function (Blueprint $table) {
            $table->id();

            $table->string('order_id', 25)->nullable(false)->comment('주문ID');
            $table->unsignedBigInteger('logistics_id')->nullable(false)->comment('물류ID');
            $table->timestamp('delivered_time')->nullable()->comment('배송 완료 시간');
            $table->string('logistics_code', 100)->nullable(false)->comment('물류 코드');
            $table->string('status', 50)->nullable(false)->comment('물류상태');
            $table->timestamp('gmt_modified')->nullable()->comment('수정시간');
            $table->timestamp('gmt_create')->nullable()->comment('생성시간');
            $table->string('logistics_company_no', 100)->nullable(false)->comment('물류 회사 번호');
            $table->string('logistics_company_name', 100)->nullable(false)->comment('물류 회사 이름');
            $table->string('logistics_bill_no', 100)->nullable(false)->comment('물류 청구서 번호');
            $table->string('sub_item_ids')->nullable(false)->comment('하위 품목 ID');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')->references('order_id')->on('order_base_datas')->onDelete('cascade');

            $table->index('order_id');
            $table->index('logistics_id');
            $table->index('logistics_code');
            $table->index('status');
        });

        DB::statement('ALTER TABLE order_logistics_datas COMMENT "W 주문 물류 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_logistics_datas');
    }
};
