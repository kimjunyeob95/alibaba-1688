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
            $table->enum('gosi_status', ["N", "Y"])->default("N")->nullable(false)->after("mapping_status")->comment('고시 적용 완료 여부 N: 적용 미완료, Y: 적용 완료');
            
            $table->index('gosi_status');
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
