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
            $table->decimal('width', 8, 2)->nullable(false)->default(0)->after("exchange_rate")->comment('가로');
            $table->decimal('length', 8, 2)->nullable(false)->default(0)->after("exchange_rate")->comment('길이');
            $table->decimal('height', 8, 2)->nullable(false)->default(0)->after("exchange_rate")->comment('높이');
            $table->decimal('weight', 8, 2)->nullable(false)->default(0)->after("exchange_rate")->comment('무게');
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
