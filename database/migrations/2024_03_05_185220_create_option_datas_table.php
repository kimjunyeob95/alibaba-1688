<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOptionDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('option_datas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('offer_id')->nullable(false)->comment('제품ID');
            $table->unsignedInteger('amount_on_sale')->nullable(false)->default(0)->comment('재고량');
            $table->unsignedInteger('price')->nullable(false)->default(0)->comment('가격(도매)');
            $table->unsignedInteger('sku_id')->nullable(false)->comment('옵션코드');
            $table->unsignedInteger('spec_id')->nullable(false)->comment('specId');
            $table->unsignedInteger('attribute_id')->nullable(false)->comment('옵션ID');
            $table->text('attribute_name')->nullable(false)->comment('옵션명');
            $table->text('attribute_trans_name')->nullable(false)->comment('옵션명(번역)');
            $table->text('sku_image_url')->nullable(true)->comment('옵션이미지');
            $table->unsignedInteger('consign_price')->nullable(false)->default(0)->comment('consignPrice');
            $table->unsignedInteger('cargo_number')->nullable(false)->default(0)->comment('cargoNumber');

            $table->timestamps();
            $table->softDeletes();

            $table->index('offer_id');
            $table->index('sku_id');
            $table->index('spec_id');
            $table->index('attribute_id');
            $table->index('cargo_number');
        });

        DB::statement('ALTER TABLE option_datas COMMENT "1688 옵션 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('option_datas');
    }
}
