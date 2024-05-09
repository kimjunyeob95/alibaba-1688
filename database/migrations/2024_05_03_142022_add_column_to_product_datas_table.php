<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToProductDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_datas', function (Blueprint $table) {
            $table->enum('trans_status_en', ["N", "Y"])->default("N")->nullable(false)->after("trans_status")->comment('영문 번역 완료 여부 N: 변역 미완료, Y: 번역 완료');
            
            $table->index('trans_status_en');
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
}
