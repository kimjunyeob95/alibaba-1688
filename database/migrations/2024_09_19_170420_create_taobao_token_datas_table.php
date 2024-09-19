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
        Schema::create('taobao_token_datas', function (Blueprint $table) {
            $table->id();
            $table->text('access_token')->nullable(false)->comment('access_token');
            $table->text('refresh_token')->nullable(false)->comment('refresh_token');

            $table->timestamp('expires_in')->nullable()->comment('access_token 만료시간');
            $table->timestamp('refresh_expires_in')->nullable()->comment('refresh_token 만료시간');

            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE taobao_token_datas COMMENT "타오바오 계정 데이터 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taobao_token_datas');
    }
};