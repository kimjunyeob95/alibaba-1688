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
            $table->string('send_goods_address_text', 30)->nullable(false)->default("")->after('weight')->comment('발송 주소');
            $table->string('pkg_size_source', 30)->nullable(false)->default("")->after('weight')->comment('무게 크기 정보 출저');
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
