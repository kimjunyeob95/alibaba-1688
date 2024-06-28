<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        DB::statement("ALTER TABLE product_modi_datas CHANGE COLUMN w_type w_type VARCHAR(10) NOT NULL DEFAULT 'W' COMMENT 'WApp type'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_modi_datas', function (Blueprint $table) {
            //
        });
    }
};
