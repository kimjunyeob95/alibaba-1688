<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductCollectLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_collect_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ["keywordQuery", "imageQuery"])->nullable(false)->comment('수집 API 타입');
            $table->enum('status', ["S", "R", "C"])->default("S")->nullable(false)->comment('수집 진행 단계 S: 대기, R: 수집중, C: 완료');
            $table->text('payload')->nullable(false)->comment('요청 payload');
            $table->unsignedInteger('log_count')->nullable(false)->comment('수집 수');
            $table->timestamp('completed_at')->nullable()->comment('완료 일자');

            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
        });

        DB::statement('ALTER TABLE product_collect_logs COMMENT "1688 상품 수집 로그 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_collect_logs');
    }
}
