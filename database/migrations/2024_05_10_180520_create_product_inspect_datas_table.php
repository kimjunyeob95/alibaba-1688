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
        Schema::create('product_inspect_datas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->string('inspect_type', 20)->nullable(false)->comment('검수 종류');
            $table->enum('is_inspect', ["Y", "N"])->nullable(false)->default("N")->comment('검수완료 여부');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');
            $table->foreign('inspect_type')->references('inspect_type')->on('inspect_datas')->onDelete('cascade');

            $table->index('offer_id');
            $table->index('inspect_type');
            $table->index('is_inspect');
        });

        DB::statement('ALTER TABLE product_inspect_datas COMMENT "WApp 상품 검수 목록 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_inspect_datas');
    }
};
