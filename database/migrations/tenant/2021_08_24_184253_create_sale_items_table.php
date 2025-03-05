<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('food_id')->unsigned();
            $table->foreign('food_id')->references('id')->on('food')->onUpdate('cascade');
            $table->bigInteger('sale_id')->unsigned();
            $table->foreign('sale_id')->references('id')->on('sales')->onUpdate('cascade');
            $table->string('food_name');
          
            $table->string('hsn')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('gst', 12, 2)->nullable();
            $table->integer('gst_percentage')->nullable();
            $table->decimal('quantity', 12, 4);
            $table->decimal('discount', 12, 2)->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->string('code')->nullable();
            $table->decimal('selling_price', 12, 2)->nullable();
            $table->decimal('cost', 12, 2)->nullable();
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
        Schema::dropIfExists('sale_items');
    }
}
