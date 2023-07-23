<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name', 100);
             $table->string('site_slogan',200);
             $table->text('address');
             $table->string('email',80);
             $table->string('order_email', 80);
             $table->string('email_from', 80);
             $table->string('contact_phone', 20);
             $table->string('fb_link', 100)->nullable();
             $table->string('twitter_link', 100)->nullable();
             $table->string('linkedin', 100)->nullable();
             $table->string('recaptcha_site_key', 100)->nullable();
             $table->text('about_site')->nullable();
             $table->integer('under_maintenence')->unsigned()->nullable();
             $table->string('maintenence_messsage',100)->nullable();
             $table->integer('max_audio_file_size')->unsigned()->nullable();
             $table->integer('max_tex_sms_limit')->unsigned()->nullable();
             $table->integer('max_voice_sms_limit')->unsigned()->nullable();
             $table->integer('tex_limit_campaing')->unsigned()->nullable();
             $table->integer('voice_limit_campaing')->unsigned()->nullable();
             $table->string('ssl_comm_user',80)->nullable();
             $table->string('ssl_comm_password',100)->nullable();
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
        Schema::dropIfExists('site_settings');
    }
}
