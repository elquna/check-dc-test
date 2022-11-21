<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLendingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lendings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('book_id');
            $table->integer('user_id');
            $table->dateTime('datetime_borrowed', $precision = 0);
            $table->dateTime('datetime_returned', $precision = 0)->nullable();
            $table->dateTime('datetime_due', $precision = 0)->nullable();
            $table->integer('points')->default(0)->comment('will be updated after book is returned');
            $table->integer('status')->default('active')->comment('active or inactive');
            $table->integer('subscriptionplan_id')->comment('the subscription plan the book falls into');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lendings');
    }
}
