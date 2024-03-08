<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable(false)->comment('카테고리ID');
            $table->string('category_name', 100)->nullable(false)->comment('카테고리명');
            $table->string('category_chinese_name', 100)->nullable(false)->comment('중국 카테고리명');
            $table->enum('leaf', ["Y", "N"])->default("N")->comment('리프 카테고리 인지 여부 Y: true, N: false');
            $table->unsignedInteger('level')->default(0)->comment('카테고리 레벨');
            $table->unsignedInteger('parent_cate_id')->comment('부모 카테고리 ID')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('category_name');
            $table->index('level');
            $table->index('parent_cate_id');
        });

        DB::statement('ALTER TABLE categories COMMENT "1688 카테고리 테이블"');
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
