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
        Schema::table('product_extend_datas', function (Blueprint $table) {
            $table->decimal('selling_point', 5, 2)->nullable(false)->after('trade_medal_level')->default(0)->comment('판매점수');

            
            $table->index('selling_point');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_extend_datas', function (Blueprint $table) {
            //
        });
    }
};
