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
            $table->renameColumn('option_name_trans', 'option_name_kr')->comment('옵션명(국문)');
            $table->text('option_name_en')->nullable(false)->after('option_name_trans')->comment('옵션명(영문)');
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
