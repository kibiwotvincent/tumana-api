<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->bigInteger('user_id');
            $table->bigInteger('reference_id')->unique();
            $table->decimal('transfer_amount');
            $table->decimal('exchange_rate');
            $table->decimal('transfer_fee');
            $table->decimal('total_amount');
            $table->decimal('amount_paid')->default(0.00);
            $table->decimal('receiver_amount');
            $table->string('receiver_phone_number');
            $table->string('receiver_name')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('orders');
    }
}
