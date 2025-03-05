<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('employee_category_id')->unsigned()->nullable();
            $table->foreign('employee_category_id')->references('id')->on('employee_categories')->onUpdate('cascade');
            $table->bigInteger('kitchen_id')->unsigned()->nullable();
            $table->foreign('kitchen_id')->references('id')->on('kitchens')->onUpdate('cascade');
            $table->string('code')->nullable();
            $table->boolean('waiter')->nullable();
            $table->boolean('login')->nullable();
            $table->string('address')->nullable();
            $table->string('area')->nullable();
            $table->string('district')->nullable();
            $table->bigInteger('phone')->nullable();
            $table->decimal('salary', 8, 2)->nullable();
            $table->string('salary_frequency')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
            $table->string('user_name');
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
        Schema::dropIfExists('employees');
    }
}
