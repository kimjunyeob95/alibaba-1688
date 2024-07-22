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
        Schema::table('product_add_datas', function (Blueprint $table) {
            $table->string('shipping_time_guarantee')->nullable(false)->default("")->after('min_order_quantity')->comment('배송 보증 기간');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_add_datas', function (Blueprint $table) {
            //
        });
    }
};
