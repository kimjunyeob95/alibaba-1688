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
        Schema::table('product_collect_logs', function (Blueprint $table) {
            $table->enum('version', ["W1", "W2"])->nullable(false)->default("W1")->after('log_count')->comment('WAPP version');
            $table->index('version');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_collect_logs', function (Blueprint $table) {
            //
        });
    }
};
