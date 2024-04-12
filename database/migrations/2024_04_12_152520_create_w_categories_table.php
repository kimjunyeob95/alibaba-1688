<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateWCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('w_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->unique()->nullable(false)->comment('카테고리ID');
            $table->string('mapping_code', 50)->default(0)->nullable(false)->comment('카테고리 맵핑 코드');
            $table->string('cate_first', 50)->nullable(true)->comment('카테고리 레벨 1');
            $table->string('cate_second', 50)->nullable(true)->comment('카테고리 레벨 2');
            $table->string('cate_third', 50)->nullable(true)->comment('카테고리 레벨 3');
            $table->string('cate_fourth', 50)->nullable(true)->comment('카테고리 레벨 4');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade');
            $table->index('category_id');
            $table->index('mapping_code');
            $table->index('cate_first');
            $table->index('cate_second');
            $table->index('cate_third');
            $table->index('cate_fourth');
        });

        DB::statement('ALTER TABLE w_categories COMMENT "W 카테고리 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('w_categories');
    }
}
