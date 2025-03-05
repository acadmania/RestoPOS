<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade');
            $table->bigInteger('purchase_id')->unsigned();
            $table->foreign('purchase_id')->references('id')->on('purchases')->onUpdate('cascade');
            $table->string('product_name')->nullable();
            $table->string('hsn')->nullable();
            $table->decimal('cost', 12, 4);
            $table->integer('gst_percentage')->nullable();
            $table->decimal('gst', 12, 4)->nullable();
            $table->decimal('discount', 12, 4)->nullable();
            $table->decimal('quantity', 12, 4);
            $table->decimal('subtotal', 12, 4);
            $table->string('batch')->nullable();
            $table->date('expiry_date')->nullable();
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
        Schema::dropIfExists('purchase_items');
    }
}
