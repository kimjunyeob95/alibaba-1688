<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangeRateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_rate_histories', function (Blueprint $table) {
            $table->id();
            $table->date("date")->comment("기준 일자");
            $table->float("exchange_rate", 8, 2)->comment("환율");
            $table->string("currency_unit")->comment("통화 코드");
            $table->timestamp('created_at')->nullable();

            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exchange_rate_histories');
    }
}
