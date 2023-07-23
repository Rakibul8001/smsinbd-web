<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduledSmses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_smses', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->integer('user_sender_id')->unsigned();
            $table->string('to_number', 25);
            $table->string('sms_type', 15);//non +/ musk
            $table->string('sms_catagory',10);//text/unicode/voice
            $table->text('sms_content');//it's length might be 1-910
            $table->tinyInteger('number_of_sms')->unsigned();
            $table->integer('total_contacts')->unsigned();
            $table->string('send_type', 10);//pending/or running
            $table->string('remarks', 255);//pending/or running
            $table->integer('contact_group_id')->unsigned();
            $table->tinyInteger('status');//error/pending/success
            $table->timestamp('submitted_at');
            //$table->timestamp('delivered_at');
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
        Schema::dropIfExists('scheduled_smses');
    }
}
