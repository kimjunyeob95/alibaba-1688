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
            $table->enum('is_except', ["Y", "N"])->nullable(false)->default("N")->after('img_type')->comment('수집 제외 여부');
            $table->index('is_except');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('genuio_image_datas', function (Blueprint $table) {
            //
        });
    }
};
