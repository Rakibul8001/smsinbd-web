<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchiveSmsSentReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive_sms_sent_report', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            $table->string('email',80);
            $table->integer('userid');
            $table->string('smsid',30);
            $table->integer('totalcampaign');
            $table->string('send_from',30);
            $table->string('sms_catagory',30);
            $table->string('sms_type',30);
            $table->string('sender_name',100);
            $table->integer('contact');
            $table->string('sms_content',255);
            $table->integer('smscount');
            $table->tinyInteger('status');
            $table->timestamp('submitted_at');
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
        Schema::dropIfExists('archive_sms_sent_report');
    }
}
