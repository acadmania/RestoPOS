<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModifierSaleItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modifier_sale_item', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sale_item_id')->unsigned()->nullable();
            $table->foreign('sale_item_id')->references('id')->on('sale_items')->onUpdate('cascade');
            $table->bigInteger('modifier_id')->unsigned()->nullable();
            $table->foreign('modifier_id')->references('id')->on('modifiers')->onUpdate('cascade');
            $table->decimal('price', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modifier_sale_item');
    }
}
