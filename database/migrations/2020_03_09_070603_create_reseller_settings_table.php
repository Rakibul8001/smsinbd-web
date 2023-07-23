<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResellerSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reseller_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->string('site_url', 200);
            $table->string('site_title', 80);
            $table->text('description');
            $table->text('meta_tag');
            $table->string('logo', 150);
            $table->string('logout_url', 150);
            $table->string('contact_number', 30);
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
        Schema::dropIfExists('reseller_settings');
    }
}
