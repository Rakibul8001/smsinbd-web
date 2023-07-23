<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountHeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('account_heads', function (Blueprint $table) {
            $table->id();
            $table->string('acc_head',50);
            $table->integer('parent_id');
            $table->tinyInteger('status');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->string('user_type',30);
            $table->string('account_type',30);
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
        Schema::dropIfExists('account_heads');
    }
}
