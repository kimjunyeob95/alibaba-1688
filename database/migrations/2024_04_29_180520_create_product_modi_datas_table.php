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
        Schema::create('product_modi_datas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->enum('w_type', ["W1", "W2"])->default("W1")->nullable(false)->comment('WApp type');
            $table->string('channel', 50)->nullable(false)->default("easySell")->comment('전송 채널');
            $table->enum('is_send', ["Y", "N", "E"])->nullable(false)->default("N")->comment('전송 여부 Y:전송 N:미전송 E:에러');
            $table->text('msg')->nullable(false)->comment('내용');
            $table->timestamp('send_dated_at')->nullable()->comment('전송 일자');

            $table->timestamps();
            $table->softDeletes();

            $table->index('offer_id');
            $table->index('w_type');
            $table->index('channel');
            $table->index('is_send');
        });

        DB::statement('ALTER TABLE product_modi_datas COMMENT "WApp 수정 상품 관리 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_modi_datas');
    }
};
