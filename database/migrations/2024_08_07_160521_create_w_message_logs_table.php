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
        Schema::create('w_message_logs', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 25)->nullable(false)->comment('주문ID');
            $table->string('channel_order_id', 25)->nullable(false)->comment('채널 주문ID');
            $table->string('code', 10)->nullable(false)->comment('메시지 코드');
            $table->text("request")->nullable(false)->comment('요청 전문');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')->references('order_id')->on('order_base_datas')->onDelete('cascade');
            $table->foreign('channel_order_id')->references('channel_order_id')->on('order_channel_datas')->onDelete('cascade');

            $table->index('order_id');
            $table->index('channel_order_id');
            $table->index('code');
        });

        DB::statement('ALTER TABLE w_message_logs COMMENT "W 메세지 콜백 로그 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('w_message_logs');
    }
};
