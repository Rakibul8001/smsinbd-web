<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            $table->string('email',80)->unique();
            $table->string('password',100);
            $table->string('company', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->integer('root_user_id')->unsigned();
            $table->integer('manager_id')->unsigned();
            $table->integer('reseller_id')->unsigned();
            $table->text('address')->nullable();
            $table->string('country',50)->nullable();
            $table->string('city',50)->nullable();
            $table->string('state',50)->nullable();
            $table->string('created_from', 20)->nullable;
            $table->string('created_by',50);
            $table->string('status',5);
            $table->string('verified',5);
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
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
        Schema::dropIfExists('users');
    }
}
