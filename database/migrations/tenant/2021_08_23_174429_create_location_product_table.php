<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_product', function (Blueprint $table) {
          $table->id();
          $table->bigInteger('location_id')->unsigned();
          $table->foreign('location_id')->references('id')->on('locations')->onUpdate('cascade');
          $table->bigInteger('product_id')->unsigned();
          $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade');
          $table->decimal('quantity', 12, 4);
          $table->string('batch')->nullable();
          $table->date('expiry_date')->nullable();
          $table->decimal('cost', 8, 2)->nullable();
          $table->boolean('cost_include_gst')->nullable();
          $table->decimal('price', 8, 2)->nullable();
          $table->boolean('price_include_gst')->nullable();
          $table->decimal('sale_price', 8, 2)->nullable();
          $table->bigInteger('supplier_id')->unsigned()->nullable();
          $table->foreign('supplier_id')->references('id')->on('suppliers')->onUpdate('cascade');
          $table->integer('gst_percentage')->nullable();
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
        Schema::dropIfExists('location_product');
    }
}
