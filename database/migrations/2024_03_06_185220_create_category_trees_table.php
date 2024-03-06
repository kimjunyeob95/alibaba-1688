<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCategoryTreesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_trees', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('category_id')->nullable(false)->comment('카테고리ID');
            $table->string('cate_first', 50)->nullable(true)->comment('카테고리 레벨 1');
            $table->string('cate_second', 50)->nullable(true)->comment('카테고리 레벨 2');
            $table->string('cate_third', 50)->nullable(true)->comment('카테고리 레벨 3');
            $table->string('cate_chinese_first', 50)->nullable(true)->comment('중국어 카테고리 레벨 1');
            $table->string('cate_chinese_second', 50)->nullable(true)->comment('중국어 카테고리 레벨 2');
            $table->string('cate_chinese_third', 50)->nullable(true)->comment('중국어 카테고리 레벨 3');

            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('cate_first');
            $table->index('cate_second');
            $table->index('cate_third');
        });

        DB::statement('ALTER TABLE category_trees COMMENT "1688 카테고리 단계별 정리 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
