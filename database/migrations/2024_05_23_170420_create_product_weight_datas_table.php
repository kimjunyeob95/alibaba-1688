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
        Schema::create('product_weight_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->string('weight_type', 20)->nullable(false)->comment('중량별 배송비 타입');
            $table->integer('weight')->nullable(false)->comment('중량');
            $table->decimal('delivery_price', 8, 2)->nullable(false)->default(12000)->comment('배송비');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');

            $table->index('offer_id');
            $table->index('weight_type');
        });

        DB::statement('ALTER TABLE product_weight_datas COMMENT "WApp 중량별 배송비 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_weight_datas');
    }
};