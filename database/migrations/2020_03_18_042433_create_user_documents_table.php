<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->string('nid',50)->nullable();
            $table->string('application',50)->nullable();
            $table->string('customppphoto',50)->nullable();
            $table->string('tradelicence',50)->nullable();
            $table->tinyInteger('isVerified')->nullable()->default(0);
            $table->integer('user_id')->unsigned()->default(0);
            $table->integer('root_user_id')->unsigned()->nullable()->default(0);
            $table->integer('manager_id')->unsigned()->nullable()->default(0);
            $table->integer('reseller_id')->unsigned()->nullable()->default(0);
            $table->string('user_type',30)->nullable();
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
        Schema::dropIfExists('user_documents');
    }
}
