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
        Schema::table('genuio_image_datas', function (Blueprint $table) {
            $table->enum('is_origin', ["Y", "N"])->nullable(false)->default("N")->after('ai_type')->comment('원본 이미지 여부');
            $table->index('is_origin');
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
