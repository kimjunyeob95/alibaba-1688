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
            $table->longText('prd_desc_en_origin')->nullable(false)->default("")->after('prd_desc')->comment('상품상제(영문)_원본');
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
