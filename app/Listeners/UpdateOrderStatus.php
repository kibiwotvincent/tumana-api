<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\StripePaymentReceived;
use App\Events\StripePaymentCompleted;

class UpdateOrderStatus
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
    public function handle(StripePaymentReceived $event)
    {
        $order = $event->order;
        if($order->amount_paid >= $order->total_amount) {
            $order->status = 'completed';
            $order->save();

            //fire stripe payment completed event so as to initiate transfer to mpesa
            event(new StripePaymentCompleted($order));
        }
    }
}
