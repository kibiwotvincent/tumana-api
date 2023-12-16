<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMpesaDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mpesa_deposits', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('OriginatorConversationID')->unique();
			$table->string('ConversationID');
			$table->double('TransactionAmount');
			$table->string('TransactionReceipt')->nullable();
			$table->string('ReceiverPhoneNumber');
			$table->string('ReceiverName')->nullable();
			$table->string('TransactionCompletedDateTime')->nullable();
			$table->string('Status');
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
        Schema::dropIfExists('mpesa_deposits');
    }
}
