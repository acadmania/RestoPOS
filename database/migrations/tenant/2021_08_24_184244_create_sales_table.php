<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onUpdate('cascade');
            $table->string('customer_name')->nullable();
            $table->string('customer_tax_number')->nullable();
            $table->decimal('total', 12, 2)->nullable();
            $table->decimal('gst', 12, 2)->nullable();

            $table->decimal('delivery', 12, 2)->nullable();
            $table->decimal('discount', 12, 2)->nullable();

              $table->json('discounts')->nullable();


            $table->decimal('grand_total', 12, 2);
            $table->string('status');
            $table->string('payment_status');
            $table->bigInteger('location_id')->unsigned()->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->onUpdate('cascade');
            $table->string('location_name')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
            $table->string('user_name');

            $table->decimal('cash_tendered', 12, 2)->nullable();
            $table->decimal('cash_balance', 12, 2)->nullable();
            $table->decimal('cash_roundoff', 12, 2)->nullable();

            $table->bigInteger('register_id')->unsigned()->nullable();
            $table->foreign('register_id')->references('id')->on('registers')->onUpdate('cascade');
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
        Schema::dropIfExists('sales');
    }
}
