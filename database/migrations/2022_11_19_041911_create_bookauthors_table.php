<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookauthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookauthors', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('book_id')->comment('id from book table');
            $table->integer('user_id')->comment('id from user table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookauthors');
    }
}
