<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_datas', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 50)->nullable(false)->comment('아이디')->unique();
            $table->string('email', 50)->nullable(false)->comment('이메일')->unique();
            $table->string('password')->comment('비밀번호');
            $table->string('name', 25)->nullable(false)->comment('관리자명');
            $table->string('level', 10)->nullable(false)->default("super")->comment('관리자 권한');

            $table->index('user_id');
            $table->index('email');
            $table->index('name');
            $table->index('level');

            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE admin_datas COMMENT "관리자 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_datas');
    }
};
