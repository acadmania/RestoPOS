<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('hsn')->nullable();
            $table->string('code')->unique()->nullable();
            $table->boolean('sellable')->nullable();
            $table->decimal('cost', 8, 2);
            $table->boolean('cost_include_gst')->nullable();
            $table->decimal('price', 8, 2)->nullable();;
            $table->boolean('price_include_gst')->nullable();
            $table->decimal('sale_price', 8, 2)->nullable();
            $table->integer('gst_percentage')->nullable();
            $table->bigInteger('product_category_id')->unsigned()->nullable();
            $table->foreign('product_category_id')->references('id')->on('product_categories');
            $table->bigInteger('product_brand_id')->unsigned()->nullable();
            $table->foreign('product_brand_id')->references('id')->on('product_brands');
           $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

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
        Schema::dropIfExists('products');
    }
}
