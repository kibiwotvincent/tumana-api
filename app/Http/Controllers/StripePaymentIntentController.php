<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Log;

class StripePaymentIntentController extends Controller
{
    /**
     * Create payment intent.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        
        $intent = PaymentIntent::create([
            'amount' => 1000,
            'currency' => 'aud',
            'payment_method_types' => ['card']
        ]);
        
        Log::debug($intent);
        
        $jsonIntent = json_decode($intent, true);
        
        return response()->json(['secret' => $intent['client_secret']]);
    }
}
