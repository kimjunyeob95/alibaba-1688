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
            $table->decimal('exchange_rate', 5, 2)->nullable(false)->default(200)->after('cargo_number')->comment('적용 환율');
            $table->index('exchange_rate');
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
