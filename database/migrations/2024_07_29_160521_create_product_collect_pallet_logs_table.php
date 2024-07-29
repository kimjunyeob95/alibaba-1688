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
        Schema::create('collect_pallet_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pallet_id')->unique()->nullable(false)->comment('파레트ID');
            $table->enum('status', ["S", "R", "C"])->default("S")->nullable(false)->comment('수집 진행 단계 S: 대기, R: 수집중, C: 완료');
            $table->timestamp('completed_at')->nullable()->comment('완료 일자');

            $table->timestamps();
            $table->softDeletes();

            $table->index('pallet_id');
            $table->index('status');
        });

        DB::statement('ALTER TABLE collect_pallet_logs COMMENT "W 상품 파레트ID 수집 로그 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collect_pallet_logs');
    }
};
