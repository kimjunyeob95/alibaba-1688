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
        Schema::create('hs_code_datas', function (Blueprint $table) {
            $table->id();
            $table->string('hs_code', 20)->unique()->nullable(false)->comment('HS부호');
            $table->date("apply_start_date")->comment("적용시작일자");
            $table->date("apply_end_date")->comment("적용종료일자");
            $table->string('ko_name')->nullable(false)->comment('한글품목명');
            $table->string('en_name')->nullable(false)->comment('영문품목명');
            $table->string('ko_trade_name')->nullable(false)->comment('한국표준무역분류명');
            $table->integer('max_unit')->nullable(false)->comment('수량단위최대단가');
            $table->integer('max_weight')->nullable(false)->comment('중량단위최대단가');
            $table->string('unit_code', 10)->nullable(false)->comment('수량단위코드');
            $table->string('weight_code', 10)->nullable(false)->comment('중량단위코드');
            $table->string('export_code', 20)->nullable(false)->comment('수출성질코드');
            $table->string('import_code', 20)->nullable(false)->comment('수입성질코드');
            $table->string('item_spec_name')->nullable(false)->comment('품목규격명');
            $table->string('required_spec_name')->nullable(false)->comment('필수규격명');
            $table->string('add_spec_name')->nullable(false)->comment('참고규격명');
            $table->string('spec_name')->nullable(false)->comment('규격설명');
            $table->string('spen_memo')->nullable(false)->comment('규격사항내용');
            $table->string('property_code', 20)->nullable(false)->comment('성질통합분류코드');
            $table->string('property_code_name')->nullable(false)->comment('성질통합분류코드명');

            $table->timestamps();
            $table->softDeletes();

            $table->index('hs_code');
            $table->index('unit_code');
            $table->index('weight_code');
            $table->index('export_code');
            $table->index('import_code');
            $table->index('property_code');
        });

        DB::statement('ALTER TABLE hs_code_datas COMMENT "관세청 hs부호 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hs_code_datas');
    }
};