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
        Schema::create('forbidden_word_datas', function (Blueprint $table) {
            $table->id();
            $table->enum('keyword_type', ["delete", "replace"])->nullable(false)->comment('delete: 삭제, replace: 교체');
            $table->text('target_keyword')->nullable(false)->comment('대상 키워드');
            $table->text('replace_keyword')->nullable(false)->comment('변경 키워드');
            $table->enum('apply_type', ["all", "prd_name", "prd_desc"])->nullable(false)->comment('적용 타입 all: 모두, prd_name: 상품명, prd_desc: 상세페이지');

            $table->timestamps();
            $table->softDeletes();

            $table->index('keyword_type');
            $table->index('apply_type');

        });

        DB::statement('ALTER TABLE forbidden_word_datas COMMENT "WApp 금칙어 관리 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forbidden_word_datas');
    }
};