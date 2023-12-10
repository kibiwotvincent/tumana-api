<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquityTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equity_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_txn_reference_id')->unique();
            $table->bigInteger('equity_reference_id')->unique();
            $table->string('transaction_id')->unique();
            $table->string('third_party_txn_id')->unique()->nullable();
            $table->integer('receiver_number')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('description')->nullable();
            $table->string('status');
            $table->timestamp('transaction_time');
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
        Schema::dropIfExists('equity_transactions');
    }
}
