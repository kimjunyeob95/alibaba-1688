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
            $table->decimal('trade_medal_level', 5, 2)->nullable(false)->after('send_etc_price')->default(0)->comment('거래 메달 레벨');
            $table->decimal('composite_service_score', 5, 2)->nullable(false)->after('send_etc_price')->default(0)->comment('종합 서비스 점수');
            $table->decimal('logistics_experience_score', 5, 2)->nullable(false)->after('send_etc_price')->default(0)->comment('물류 경험 점수');
            $table->decimal('dispute_complaint_score', 5, 2)->nullable(false)->after('send_etc_price')->default(0)->comment('분쟁/불만 점수');
            $table->decimal('offer_experience_score', 5, 2)->nullable(false)->after('send_etc_price')->default(0)->comment('제안 경험 점수');
            $table->decimal('consulting_experience_score', 5, 2)->nullable(false)->after('send_etc_price')->default(0)->comment('상담 경험 점수');
            $table->decimal('trade_score', 5, 2)->nullable(false)->after('send_etc_price')->default(0)->comment('거래 점수');

            $table->index('trade_medal_level');
            $table->index('composite_service_score');
            $table->index('logistics_experience_score');
            $table->index('dispute_complaint_score');
            $table->index('offer_experience_score');
            $table->index('consulting_experience_score');
            $table->index('trade_score');
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
