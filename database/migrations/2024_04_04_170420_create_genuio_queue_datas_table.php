<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateGenuioQueueDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genuio_queue_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->longText('payload_json')->nullable(false)->comment('요청 전문');
            $table->enum('request_user', ["onchannel", "genuio"])->nullable(false)->comment('요청자');
            $table->longText('response_json')->nullable(false)->comment('응답 전문');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('offer_id')->references('offer_id')->on('product_datas')->onDelete('cascade');
            $table->index('offer_id');
            $table->index('request_user');
        });

        DB::statement('ALTER TABLE genuio_queue_datas COMMENT "1688, genuio 양방향 통신 기본 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genuio_queue_datas');
    }
}
