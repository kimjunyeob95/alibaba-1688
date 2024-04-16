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
        Schema::table('genuio_queue_datas', function (Blueprint $table) {
            $table->string('send_type', 25)->nullable(false)->default("imgTrans")->after('parent_id')->comment('통신 타입');
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
        Schema::table('genuio_queue_datas', function (Blueprint $table) {
            //
        });
    }
};
