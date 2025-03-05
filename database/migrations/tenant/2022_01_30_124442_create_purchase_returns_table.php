<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('purchase_id')->unsigned()->nullable();
            $table->foreign('purchase_id')->references('id')->on('purchases')->onUpdate('cascade');
            $table->bigInteger('supplier_id')->unsigned();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onUpdate('cascade');
            $table->string('supplier_name')->nullable();
            $table->string('supplier_tax_number')->nullable();
            $table->decimal('total', 12, 4)->nullable();
            $table->decimal('gst', 12, 4)->nullable();

            $table->decimal('shipping', 12, 4)->nullable();
            $table->decimal('discount', 12, 4)->nullable();
            $table->decimal('grand_total', 12, 4);
            $table->string('status'); //ordered, recieved, pending
            $table->string('payment_status'); //unpaid, partially paid, paid
            $table->bigInteger('location_id')->unsigned()->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->onUpdate('cascade');
            $table->string('location_name')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
            $table->string('user_name');
            $table->json('extras')->nullable();

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
        Schema::dropIfExists('purchase_returns');
    }
}
