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
        Schema::table('product_datas', function (Blueprint $table) {
            $table->integer('sold_out')->default(0)->nullable(false)->after("prd_rule")->comment('판매량');
            
            $table->index('prd_rule');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_datas', function (Blueprint $table) {
            //
        });
    }
};
