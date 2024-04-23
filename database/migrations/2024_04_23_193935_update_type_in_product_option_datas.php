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
        Schema::table('product_option_datas', function (Blueprint $table) {
            $table->decimal('price_1688', 12, 2)->change();
            $table->decimal('option_price', 12, 2)->change();
            $table->decimal('onch_price', 12, 2)->change();
            $table->decimal('cus_price', 12, 2)->change();
            $table->decimal('recom_cus_price', 12, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_option_datas', function (Blueprint $table) {
            //
        });
    }
};
