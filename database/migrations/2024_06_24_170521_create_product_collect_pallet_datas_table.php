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
        Schema::create('product_collect_pallet_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pallet_id')->nullable(false)->comment('파레트ID');
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');

            $table->index('pallet_id');
            $table->index('offer_id');
        });

        DB::statement('ALTER TABLE product_collect_pallet_datas COMMENT "W 상품 파레트ID 수집 리스트 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_collect_pallet_datas');
    }
};
