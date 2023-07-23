<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperatorPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operator_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->tinyInteger('operator_id')->unsigned();
            $table->float('pay_amount',12,2)->unsigned();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamp('paid_at');
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
        Schema::dropIfExists('operator_payments');
    }
}
