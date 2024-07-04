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
        Schema::table('product_extend_datas', function (Blueprint $table) {
            $table->decimal('after_sales_experience_score', 5, 2)->nullable(false)->after('trade_score')->default(0)->comment('반품 교환 경험 지수');
            $table->decimal('repeat_purchase_percent', 5, 2)->nullable(false)->after('trade_score')->default(0)->comment('재구매율');

            
            $table->index('after_sales_experience_score');
            $table->index('repeat_purchase_percent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_extend_datas', function (Blueprint $table) {
            //
        });
    }
};
