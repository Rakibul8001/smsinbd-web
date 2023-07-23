<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsSendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_senders', function (Blueprint $table) {
            $table->id();
            $table->string('sender_name', 100);
            $table->integer('operator_id')->unsigned()->nullable();
            $table->tinyInteger('status')->unsigned();
            $table->tinyInteger('default')->unsigned();
            $table->string('user', 60)->nullable();
            $table->string('password', 100)->nullable();
            $table->text('gateway_info')->nullable();
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
        Schema::dropIfExists('sms_senders');
    }
}
