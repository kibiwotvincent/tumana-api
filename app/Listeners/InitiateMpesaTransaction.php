<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\EquityTransaction;
use App\Events\PaypalTransactionCompleted;

class InitiateMpesaTransaction
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(PaypalTransactionCompleted $event)
    {
        $transaction = $event->transaction;
        $amount = $transaction->receiver_amount;
        $receiver = $transaction->receiver_phone_number;
        $reference = $transaction->reference_id;
        $response = EquityTransaction::sendMoney($amount, $receiver, $reference);
        
        if($response['status'] == false) {
            //log error message
            print($response['message']);
            return;
        }
        
        print('acknowledging transaction...');
        EquityTransaction::acknowledgeTransaction($parentTransactionReferenceID = $transaction->reference_id, $transaction = $response);
    }
}
