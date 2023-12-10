<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Transaction;
use App\Events\EquityTransactionCompleted;

class UpdatePaypalTransactionCreditStatus
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
    public function handle(EquityTransactionCompleted $event)
    {
        $transaction = $event->transaction;
        if($transaction->status == 'completed') {
            $paypalTxn = Transaction::where('reference', $transaction->parent_txn_reference_id)->first();
            $paypalTxn->credit_status = 'completed';
            $paypalTxn->save();
        }
    }
}
