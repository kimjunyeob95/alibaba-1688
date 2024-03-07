<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductNoticeDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_notice_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->unsignedInteger('attribute_id')->nullable(false)->comment('고시ID');
            $table->unsignedInteger('notice_type')->default(26)->nullable(false)->comment('상품고시구분');
            $table->string('attribute_name', 100)->nullable(false)->comment('고시이름');
            $table->string('attribute_value', 100)->nullable(false)->comment('고시값');
            $table->string('attribute_name_trans', 100)->nullable(false)->comment('고시이름_번역');
            $table->string('attribute_value_trans', 100)->nullable(false)->comment('고시값_번역');

            $table->timestamps();
            $table->softDeletes();

            $table->index('offer_id');
            $table->index('attribute_id');
        });

        DB::statement('ALTER TABLE product_notice_datas COMMENT "1688 상품 고시정보 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_notice_datas');
    }
}
