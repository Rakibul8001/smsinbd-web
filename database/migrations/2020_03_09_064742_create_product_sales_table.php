<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_sales', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->string('transection_id',50)->index();
            $table->string('sms_category',30)->index();
            $table->integer('qty')->unsigned();
            $table->float('price',12,2)->unsigned();
            $table->string('validity_period', 40);
            $table->integer('invoice_vat')->unsigned();
            $table->float('vat_amount',10,2)->unsigned();
            $table->timestamp('invoice_date')->index();
            $table->string('invoice_owner_type',30)->index();
            $table->integer('invoice_owner_id')->unsigned()->index();
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
        Schema::dropIfExists('product_sales');
    }
}
