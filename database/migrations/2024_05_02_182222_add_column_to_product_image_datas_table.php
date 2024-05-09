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
        Schema::table('product_image_datas', function (Blueprint $table) {
            $table->enum('lang', ["kr", "en"])->nullable(false)->default("kr")->after('img_type')->comment('이미지 번역 타입');
            $table->index('lang');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_image_datas', function (Blueprint $table) {
            //
        });
    }
};
