<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->string('hsn')->nullable();
            $table->decimal('cost', 8, 2)->nullable();
            $table->decimal('price', 8, 2);
            $table->boolean('price_include_gst')->nullable();
            $table->string('image')->nullable();
            $table->boolean('cost_include_gst')->nullable();
            $table->decimal('sale_price', 8, 2)->nullable();
            $table->integer('gst_percentage')->nullable();
            $table->bigInteger('food_category_id')->unsigned()->nullable();
            $table->foreign('food_category_id')->references('id')->on('food_categories');
            $table->bigInteger('food_brand_id')->unsigned()->nullable();
            $table->foreign('food_brand_id')->references('id')->on('food_brands');
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
