<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOcCategoryCodeToCategoryMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_mappings', function (Blueprint $table) {
            // oc_category_code 컬럼 추가
            $table->unsignedInteger('oc_category_code')->nullable(false)->default(0)->comment('온채널 카테고리 맵핑 코드 onch_category_excel_data_copy2 FK codenum');

            $table->index('oc_category_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_mappings', function (Blueprint $table) {
            // oc_category_code 컬럼 제거
            $table->dropColumn('oc_category_code');
        });
    }
}
