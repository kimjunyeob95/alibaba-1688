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
        Schema::create('oc_ge_queue_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable(false)->comment('큐ID');
            $table->string('send_type', 25)->nullable(false)->default("imgTrans")->comment('통신 타입');
            $table->longText('payload_json')->nullable(false)->comment('요청 전문');
            $table->enum('request_user', ["onchannel", "genuio"])->nullable(false)->comment('요청자');
            $table->longText('response_json')->nullable(false)->comment('응답 전문');

            $table->timestamps();
            $table->softDeletes();

            $table->index('parent_id');
            $table->index('request_user');
        });

        DB::statement('ALTER TABLE oc_ge_queue_datas COMMENT "onchannel, genuio 양방향 통신 기본 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oc_ge_queue_datas');
    }
};