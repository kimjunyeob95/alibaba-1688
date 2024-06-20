<?php

use App\Constants\EasySellConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterWTypeEasysellProductLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update the enum values
        DB::statement("ALTER TABLE `easysell_product_logs` CHANGE `w_type` `w_type` ENUM('W', 'DropHub') NOT NULL");

        // Update the column values
        DB::table('easysell_product_logs')->where('account', EasySellConstant::USER_ID_W)->update(['w_type' => EasySellConstant::TYPE_W]);
        DB::table('easysell_product_logs')->where('account', EasySellConstant::USER_ID_DROPHUB)->update(['w_type' => EasySellConstant::TYPE_DROPHUB]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert the enum values
        DB::statement("ALTER TABLE `easysell_product_logs` CHANGE `w_type` `w_type` ENUM('W1', 'W2') NOT NULL");

        // Revert the column values
        DB::table('easysell_product_logs')->where('account', EasySellConstant::USER_ID_W)->update(['w_type' => EasySellConstant::TYPE_W]);
        DB::table('easysell_product_logs')->where('account', EasySellConstant::USER_ID_DROPHUB)->update(['w_type' => EasySellConstant::TYPE_DROPHUB]);
    }
}
