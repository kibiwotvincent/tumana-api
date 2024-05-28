<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\EquityTransaction;
use App\Models\MpesaDeposit;
use App\Events\StripePaymentCompleted;
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
    public function handle(StripePaymentCompleted $event)
    {
        $order = $event->order;
        $amount = $order->receiver_amount;
        Log::info('Initiating mpesa transfer...');
    }
}
