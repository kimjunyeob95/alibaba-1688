<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductSearchDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_search_datas', function (Blueprint $table) {
            $table->id();
            $table->text('search_title')->nullable(false)->comment('검색 제목');
            $table->string('search_type', 25)->default("url")->nullable(false)->comment('검색 타입 url: url로 검색');
            $table->enum('status', ["S", "R", "C"])->default("S")->nullable(false)->comment('수집 진행 단계 S: 대기, R: 수집중, C: 완료');
            $table->unsignedInteger('search_count')->nullable(false)->comment('조회 수');
            $table->timestamp('completed_at')->nullable()->comment('완료 일자');

            $table->timestamps();
            $table->softDeletes();

            $table->index('search_type');
            $table->index('status');
            $table->index('search_count');
        });

        DB::statement('ALTER TABLE product_search_datas COMMENT "1688 상품 url 검색 기본 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_search_datas');
    }
}
