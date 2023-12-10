<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EquityTransaction;
use Log;
use App\Models\Transaction;
use App\Events\PaypalTransactionCompleted;

class EquityTransactionController extends Controller
{
    public function handleCallback(Request $request) {
        $data = $request->all();
        Log::debug($data);
        
        if($data['status'] == true) {
            EquityTransaction::confirmTransaction($data);
        }
    }
    
    public function test() {
        $transaction = Transaction::first();
        event(new PaypalTransactionCompleted($transaction));
    }
}
