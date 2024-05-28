<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Log;

class StripePaymentIntent extends Model
{
    use HasFactory;

    /**
     * Create payment intent.
     *
     * @param  \App\Models\Order  $amount
     * @return String
     */
    public static function createPaymentIntent(Order $order)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        
        $amount = intval($order->total_amount * 100);
    
        $currency = 'aud';
        $intent = PaymentIntent::create([
            'amount' => $amount,
            'currency' => $currency,
            'payment_method_types' => ['card'],
            'metadata' => ['order_uuid' => $order->uuid]
        ]);
        
        Log::debug($intent);
        
        return $intent['client_secret'];
    }
}
