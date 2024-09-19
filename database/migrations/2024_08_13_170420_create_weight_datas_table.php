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
        Schema::create('weight_datas', function (Blueprint $table) {
            $table->id();
            $table->decimal('weight', 5, 1)->nullable(false)->comment('중량');
            $table->integer('shipping_price')->nullable(false)->comment('해운배송비');
            $table->integer('air_shipping_price')->nullable(false)->comment('항공배송비');

            $table->timestamps();
            $table->softDeletes();

            $table->index('weight');
            $table->index('shipping_price');
            $table->index('air_shipping_price');
        });

        DB::statement('ALTER TABLE weight_datas COMMENT "WApp 중량별 배송비 목록 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weight_datas');
    }
};