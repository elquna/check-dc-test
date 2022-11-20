<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersubscriptionplansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usersubscriptionplans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('user_id')->default(0);
            $table->integer('subscriptionplan_id')->comment('id from subscriptionplans table');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('active')->comment('active or inactive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usersubscriptionplans');
    }
}
