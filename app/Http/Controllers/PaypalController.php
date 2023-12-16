<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Log;
use App\Models\Transaction;
use App\Events\PaypalTransactionCompleted;
use Carbon\Carbon;

class PaypalController extends Controller
{
    /**
     * Paypal create order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrder(Request $request)
    {
        $provider = new PayPalClient;
        
        $transferAmount = $request->order[0]['transfer_amount'];
        $receiverPhoneNumber = $request->order[0]['receiver_phone_number'];
        $transferFee = round($transferAmount * 0.1, 2);
        $exchangeRate = 101.00;
        
        $provider->getAccessToken();
        $data = [
                'intent' => 'CAPTURE',
                'application_context' => [
                    'shipping_preference' => "NO_SHIPPING",
                ],
                'purchase_units' => [
                    [
                        'amount' => [
                          'currency_code' => "AUD",
                          'value' => $transferAmount
                        ]
                    ]
                ]
        ];
        
        $provider->getAccessToken();
        $order = $provider->createOrder($data);
        $reference = Carbon::now()->format('YmdHis');
        
        if(isset($order['id'])) {
           //save transaction to database
            $transaction = new Transaction;
            $transaction->user_id = $request->user()->id;
            $transaction->reference_id = $reference;
            $transaction->paypal_order_id = $order['id'];
            $transaction->transfer_amount = $transferAmount;
            $transaction->exchange_rate = $exchangeRate;
            $transaction->transfer_fee = $transferFee;
            $transaction->total_amount = $transferAmount;
            $transaction->receiver_amount = floor(($transferAmount - $transferFee) * $exchangeRate);
            $transaction->receiver_phone_number = $receiverPhoneNumber;
            $transaction->paypal_status = 'pending';
            $transaction->credit_status = 'pending';
            $transaction->save();
        }
        
        return $order;
    }
    
    public function captureOrder(Request $request)
    {
        $provider = new PayPalClient;
        $orderID = $request->id;
        $provider->getAccessToken();
        $order = $provider->capturePaymentOrder($orderID);
        $paypalTransaction = $order['purchase_units'][0]['payments']['captures'][0];
        
        $transaction = Transaction::where('paypal_order_id', $orderID)->first();
        $transaction->paypal_transaction_id = $paypalTransaction['id'];
        if($paypalTransaction['status'] == "COMPLETED") {
            $transaction->paypal_status = 'completed';
        }
        $transaction->save();
        
        if($paypalTransaction['status'] == "COMPLETED") {
            event(new PaypalTransactionCompleted($transaction));
        }
        
        return $order;
    }
}
