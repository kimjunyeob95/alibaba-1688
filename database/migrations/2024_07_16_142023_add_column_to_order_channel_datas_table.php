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
            $table->decimal('delivery_price', 8, 2)->nullable(false)->after('total_quantity')->default(0.0)->comment('채널별 배송비');
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
