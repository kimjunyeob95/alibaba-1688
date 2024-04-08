<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToGenuioQueueDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('genuio_queue_datas', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable(false)->default(0)->after('offer_id')->comment('부모 queue ID');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('genuio_queue_datas', function (Blueprint $table) {
            //
        });
    }
}
