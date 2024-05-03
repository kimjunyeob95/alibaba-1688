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
        Schema::table('product_notice_datas', function (Blueprint $table) {
            $table->renameColumn('attribute_name_trans', 'attribute_name_kr')->comment('고시이름(국문)');
            $table->renameColumn('attribute_value_trans', 'attribute_value_kr')->comment('고시값(국문)');
            $table->text('attribute_name_en')->nullable(false)->after('attribute_value_trans')->comment('고시이름(영문)');
            $table->text('attribute_value_en')->nullable(false)->after('attribute_name_en')->comment('고시값(영문)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_notice_datas', function (Blueprint $table) {
            //
        });
    }
};
