<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade');
            $table->bigInteger('table_id')->unsigned()->nullable();
            $table->foreign('table_id')->references('id')->on('tables')->onUpdate('cascade');
            $table->string('table_name')->nullable();
            $table->bigInteger('food_id')->unsigned()->nullable();
            $table->foreign('food_id')->references('id')->on('food')->onUpdate('cascade');
            $table->string('food_name')->nullable();
            $table->string('hsn')->nullable();
            $table->string('code')->nullable();
            $table->decimal('quantity', 12, 4);
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('selling_price', 12, 2)->nullable();
            $table->decimal('gst', 12, 2)->nullable();
            $table->integer('gst_percentage')->nullable();
            $table->decimal('discount', 12, 2)->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->boolean('printed')->nullable();

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
        Schema::dropIfExists('table_items');
    }
}
