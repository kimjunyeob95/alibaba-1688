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
            $table->string('batch_number')->nullable(false)->default(0)->after('min_order_quantity')->comment('min_order_quantity 의 구매 단위');
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
