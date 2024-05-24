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
        Schema::create('product_except_datas', function (Blueprint $table) {
            $table->id();
            $table->string('except_type', 20)->nullable(false)->comment('제외 타입');
            $table->unsignedBigInteger('attribute_id')->nullable(false)->comment('고시ID');
            $table->enum('is_except', ["Y", "N"])->default("N")->nullable(false)->comment('제외여부');
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('except_type');
            $table->index('attribute_id');
            $table->index('is_except');
        });

        DB::statement('ALTER TABLE product_except_datas COMMENT "WApp 제외 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_except_datas');
    }
};