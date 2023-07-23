<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransectionDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transection_documents', function (Blueprint $table) {
            $table->id();
            $table->integer('trans_id')->unsigned();
            $table->string('payment_through', 100);
            $table->string('bnk_acc_no', 30);
            $table->string('bnk_slip_no', 30);
            $table->string('bks_trans_id', 30);
            $table->string('order_id', 30);
            $table->text('remarks');
            $table->integer('user_id')->unsigned();
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
        Schema::dropIfExists('transection_documents');
    }
}
