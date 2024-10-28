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
        Schema::table('bonaera_out_delivery_datas', function (Blueprint $table) {
            $table->enum('ctr_num', ["1", "2"])->default("1")->nullable(false)->after('invoice')->comment('운송방법 1: 항공, 2: 해운');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bonaera_out_delivery_datas', function (Blueprint $table) {
            //
        });
    }
};
