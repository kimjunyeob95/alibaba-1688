<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateApiUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_users', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 100)->nullable(false)->unique()->comment('유저ID');
            $table->string('user_company', 100)->nullable(false)->comment('유저 업체');

            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('user_company');
        });

        DB::statement('ALTER TABLE api_users COMMENT "1688 openAPI 유저 테이블"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_users');
    }
}
