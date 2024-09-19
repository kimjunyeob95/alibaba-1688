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
        Schema::table('category_weight_datas', function (Blueprint $table) {
            $table->dropColumn('delivery_price');

            $table->decimal('weight', 6, 2)->nullable(false)->comment('중량')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_weight_datas', function (Blueprint $table) {
            //
        });
    }
};
