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
        Schema::table('order_channel_datas', function (Blueprint $table) {
            $table->string('shipping_type', 5)->nullable(false)->after('channel_order_id')->default("OF")->comment('운송방식');
            $table->string('clearance_type', 5)->nullable(false)->after('channel_order_id')->default("SA")->comment('통관유형');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_channel_datas', function (Blueprint $table) {
            //
        });
    }
};
