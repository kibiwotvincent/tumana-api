<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->bigInteger('reference_id')->unique();
            $table->string('paypal_order_id')->unique();
            $table->string('paypal_transaction_id')->unique()->nullable();
            $table->double('transfer_amount');
            $table->double('exchange_rate');
            $table->double('transfer_fee');
            $table->double('total_amount');
            $table->double('receiver_amount');
            $table->string('receiver_phone_number');
            $table->string('paypal_status');
            $table->string('credit_status');
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
        Schema::dropIfExists('transactions');
    }
}
