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
        Schema::create('category_weight_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->unique()->nullable(false)->comment('카테고리ID');
            $table->smallInteger('weight')->nullable(false)->default(0)->comment('무게');
            $table->decimal('delivery_price', 8, 2)->nullable(false)->default(12000)->comment('배송비');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade');

            $table->index('category_id');
            $table->index('weight');
            $table->index('delivery_price');

        });

        DB::statement('ALTER TABLE category_weight_datas COMMENT "WApp 카테고리 별 중량 배송비 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_weight_datas');
    }
};
