<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCategoryMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('category_id')->nullable(false)->comment('카테고리ID');
            $table->string('mapping_channel', 50)->nullable(false)->default("onchannel")->comment('카테고리 맵핑 채널');
            $table->string('mapping_code', 50)->nullable(false)->comment('카테고리 맵핑 코드');

            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('mapping_channel');
            $table->index('mapping_code');
        });

        DB::statement('ALTER TABLE category_mappings COMMENT "1688 카테고리 채널별 맵핑 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
