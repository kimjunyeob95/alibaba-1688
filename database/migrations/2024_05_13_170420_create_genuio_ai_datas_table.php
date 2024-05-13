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
        Schema::create('genuio_ai_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->string('ai_apply', 25)->nullable(false)->comment('AI 알고리즘 적용 위치');
            $table->longText('origin_data')->nullable(false)->comment('AI 적용 전 원본 데이터');
            $table->longText('apply_data')->nullable(false)->comment('AI 적용 데이터');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');
            $table->index('ai_apply');
        });

        DB::statement('ALTER TABLE genuio_ai_datas COMMENT "genuio AI 정보 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genuio_ai_datas');
    }
};
