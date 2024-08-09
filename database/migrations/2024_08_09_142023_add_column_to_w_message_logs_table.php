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
        Schema::table('w_message_logs', function (Blueprint $table) {
            $table->enum('pub_sub_is_send', ["Y", "N"])->default("Y")->after('request')->nullable(false)->comment('pub/sub 전송 여부');
            $table->text("pub_sub_msg")->nullable(false)->after('request')->comment('pub/sub 전송 전문');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('w_message_logs', function (Blueprint $table) {
            //
        });
    }
};
