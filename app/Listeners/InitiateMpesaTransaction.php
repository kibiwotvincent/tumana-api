<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\EquityTransaction;
use App\Models\MpesaDeposit;
use App\Events\StripePaymentCompleted;
use Log;
use IntaSend\IntaSendPHP\Transfer;

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
    public function handle(StripePaymentCompleted $event)
    {
        $order = $event->order;
        $amount = intval($order->receiver_amount);
        $receiverPhoneNumber = $order->receiver_phone_number;
        Log::info('Initiating mpesa transfer...');

        $receiverPhoneNumber = '254'.substr($receiverPhoneNumber, 1, strlen($receiverPhoneNumber) - 1);

        $transactions = [
            ['account'=>$receiverPhoneNumber, 'amount'=> $amount],
        ];
        Log::info($transactions);
        $credentials = [
            'token'=> config('services.intasend.api_token'),
            'publishable_key'=> config('services.intasend.publishable_key'),
            'test' => false
        ];
        
        $transfer = new Transfer();
        $transfer->init($credentials);
        
        $response=$transfer->mpesa("KES", $transactions);
        $response = $transfer->approve($response);

        Log::debug("Mpesa B2C".$response);
    }
}
