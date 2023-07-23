<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResellerSendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reseller_senders', function (Blueprint $table) {
            $table->id();
            $table->integer('reseller_id')->unsigned();
            $table->integer('sms_sender_id')->unsigned();
            $table->tinyInteger('status')->unsigned();
            $table->tinyInteger('default')->unsigned();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
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
        Schema::dropIfExists('reseller_senders');
    }
}
