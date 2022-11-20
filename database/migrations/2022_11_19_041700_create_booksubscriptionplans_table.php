<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksubscriptionplansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booksubscriptionplans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('subscriptionplan_id')->comment('id from subscriptionplans table');
            $table->integer('book_id')->comment('id from book table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booksubscriptionplans');
    }
}
