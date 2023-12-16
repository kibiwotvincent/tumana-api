<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\EquityTransaction;
use App\Models\MpesaDeposit;
use App\Events\PaypalTransactionCompleted;
use Log;

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
        
        /**
        **send with equity**
        $response = EquityTransaction::sendMoney($amount, $receiver, $reference);
        
        if($response['status'] == false) {
            //log error message
            print($response['message']);
            return;
        }
        
        print('acknowledging transaction...');
        EquityTransaction::acknowledgeTransaction($parentTransactionReferenceID = $transaction->reference_id, $transaction = $response);
        **end send with equity**/
        
        //send with mpesa
        //initiate send money request
		$mpesaDeposit = new MpesaDeposit();
		$response = $mpesaDeposit->sendMoney($receiver, $amount, $reference);
		if(isset($response['errorCode'])) {
            //log error
		    Log::info("Failed to send money to ".$receiver.". Try again later.");
		}
		else {
            $deposit['TransactionAmount'] = $amount;
            $deposit['ReceiverPhoneNumber'] = $receiver;
            $deposit['OriginatorConversationID'] = $response['OriginatorConversationID'];
            $deposit['ConversationID'] = $response['ConversationID'];
            
            $mpesaDeposit->acknowledgeDeposit($deposit);
			Log::info("Payment has been send to ".$receiver.".");
		}
    }
}
