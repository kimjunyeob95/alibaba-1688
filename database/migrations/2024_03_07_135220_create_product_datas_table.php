<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id')->nullable(false)->unique()->comment('제품ID');
            $table->unsignedBigInteger('category_id')->nullable(false)->comment('카테고리ID');
            $table->text('prd_name')->nullable(false)->comment('제품명');
            $table->text('prd_name_trans')->nullable(false)->comment('제품명_번역');
            $table->longText('prd_desc')->nullable(false)->comment('제품상세');
            $table->enum('tax_type', [1, 2])->default(1)->nullable(false)->comment('과세여부 1: 과세, 2: 비과세');
            $table->enum('minor_not_sale', ["Y", "N"])->default("N")->nullable(false)->comment('미성년자판매금지');
            $table->string('delivery_name', 50)->nullable(false)->comment('택배사');
            $table->longText('delivery_info')->nullable(false)->comment('배송마감/발송처/발송일');
            $table->longText('return_comment')->nullable(false)->comment('반품사항안내');
            $table->enum('supply_type', [1, 2, 3])->default(2)->nullable(false)->comment('공급업체 분류 1: 제조사, 2: 벤더사, 3: 수입사');
            $table->unsignedInteger('prd_channel')->default(22)->nullable(false)->comment('제품채널');
            $table->unsignedInteger('prd_rule')->default(1)->nullable(false)->comment('판매가 준수여부');
            $table->text('main_img_origin')->nullable(false)->comment('제품 메인 이미지 원본');
            $table->text('main_img_trans')->nullable(false)->comment('제품 메인 이미지 번역');
            $table->string('supply_code', 100)->nullable(false)->comment('공급사코드');
            $table->longText('response_json')->nullable(false)->comment('응답 전문');

            $table->timestamps();
            $table->softDeletes();

            $table->index('offer_id');
            $table->index('category_id');
            $table->index('supply_type');
            $table->index('supply_code');
        });

        DB::statement('ALTER TABLE product_datas COMMENT "1688 상품 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_datas');
    }
}
