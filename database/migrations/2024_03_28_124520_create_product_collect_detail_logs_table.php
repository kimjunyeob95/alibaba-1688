<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductCollectDetailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_collect_detail_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('log_id')->nullable(false)->comment('product_collect_logs id');
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->enum('is_collect', ["Y", "N"])->default("N")->nullable(false)->comment('수집 성공 여부');
            $table->text('msg')->nullable(false)->comment('내용');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('log_id')->references('id')->on('product_collect_logs')->onDelete('cascade');
            $table->index('offer_id');
            $table->index('log_id');
            $table->index('is_collect');
        });

        DB::statement('ALTER TABLE product_collect_detail_logs COMMENT "1688 상품 수집 상세 로그 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_collect_detail_logs');
    }
}
