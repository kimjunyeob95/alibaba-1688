<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('onchannel_product_logs', function (Blueprint $table) {
            $table->string('send_type', 10)->nullable(false)->default("30")->after('member_id')->comment('전송 종류 30: 일반상품, 28: 사입상품');

            $table->index('send_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('onchannel_product_logs', function (Blueprint $table) {
            //
        });
    }
};
