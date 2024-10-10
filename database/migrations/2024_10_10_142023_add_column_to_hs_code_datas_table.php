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
        Schema::table('hs_code_datas', function (Blueprint $table) {
            $table->string('sh_no', 20)->nullable(false)->after("hs_code")->comment('품목번호');

            $table->index('sh_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hs_code_datas', function (Blueprint $table) {
            //
        });
    }
};
