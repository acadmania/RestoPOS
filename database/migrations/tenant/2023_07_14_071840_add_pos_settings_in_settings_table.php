<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPosSettingsInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('pos_default_payment_method')->nullable();
            $table->string('pos_default_order_type')->nullable();
            $table->boolean('pos_auto_fill_cash_amount')->nullable();
            $table->string('pos_post_product_add_action')->default('New Row');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('pos_default_payment_method');
            $table->dropColumn('pos_default_order_type');
            $table->dropColumn('pos_auto_fill_cash_amount');
            $table->dropColumn('pos_post_product_add_action');
        });
    }
}
