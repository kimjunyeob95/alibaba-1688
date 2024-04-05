<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChannelToOrderDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_datas', function (Blueprint $table) {
            $table->string('channel', 25)->nullable(false)->after('order_id')->comment('주문 채널');
            $table->index('channel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_datas', function (Blueprint $table) {
            //
        });
    }
}
