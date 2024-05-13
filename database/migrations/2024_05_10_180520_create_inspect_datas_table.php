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
        Schema::create('inspect_datas', function (Blueprint $table) {
            $table->id();

            $table->string('inspect_type', 20)->unique()->nullable(false)->comment('검수 종류');
            $table->string('inspect_title', 50)->nullable(false)->comment('검수 이름');

            $table->timestamps();
            $table->softDeletes();

            $table->index('inspect_type');
        });

        DB::statement('ALTER TABLE inspect_datas COMMENT "WApp 검수 목록 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inspect_datas');
    }
};
