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
        Schema::table('product_datas', function (Blueprint $table) {
            $table->enum('w_type', ["W1", "W2"])->default("W1")->nullable(false)->after('status')->comment('WApp type');
            $table->renameColumn('prd_name_trans', 'prd_name_kr')->comment('제품명(국문)');
            $table->text('prd_name_en')->nullable(false)->after('prd_name_trans')->comment('제품명(영문)');
            $table->renameColumn('prd_desc_trans', 'prd_desc_kr')->comment('제품상세(국문)');
            $table->longText('prd_desc_en')->nullable(false)->after('prd_desc_trans')->comment('제품상세(영문)');
            
            $table->index('w_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_datas', function (Blueprint $table) {
            //
        });
    }
};
