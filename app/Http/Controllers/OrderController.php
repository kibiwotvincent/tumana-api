<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\NewOrderRequest;
use Illuminate\Http\JsonResponse;
use App\Models\Order;
use App\Models\StripePaymentIntent;
use Str;
use Stripe\Stripe;
use Stripe\Webhook;
use Log;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    /**
     * Handle an incoming create new order request.
     *
     * @param  \App\Http\Requests\NewOrderRequest  $request
     * @return \Illuminate\Http\JsonResponse;
     */
    public function create(NewOrderRequest $request) : JsonResponse
    {
        $formData = $request->safe();
        $referenceID = time();
        $transferAmount = $formData->transfer_amount;
        $exchangeRate = 88.55;
        $transferFee = 0.50;
        $totalAmount = round($transferAmount + $transferFee);
        $receiverAmount = floor($transferAmount * $exchangeRate);

        $order = new Order;
        $order->uuid = Str::uuid();
        $order->user_id = $request->user()->id;
        $order->reference_id = $referenceID;
        $order->transfer_amount = $transferAmount;
        $order->exchange_rate = $exchangeRate;
        $order->transfer_fee = $transferFee;
        $order->total_amount = $totalAmount;
        $order->receiver_amount = $receiverAmount;
        $order->receiver_phone_number = $formData->receiver_phone_number;
        $order->status = 'pending';
        $order->credit_status = 'pending';
        $order->save();

        $order = Order::where('reference_id', $referenceID)->first();
        $clientSecret = StripePaymentIntent::createPaymentIntent($order);

        return response()->json(['client_secret' => $clientSecret, 'order' => $order, 'message' => "Order created successfully."], 200);
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                // Then define and call a method to handle the successful payment intent.
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;
            // Add more cases for other event types you want to handle
            default:
                Log::info('Received unknown event type ' . $event->type);
        }

        return response()->json(['status' => 'success'], 200);
    }

    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        // Handle the successful payment intent.
        Log::info('Payment Intent Succeeded:', ['paymentIntent' => $paymentIntent]);
        // Add your logic here to handle the successful payment.
    }

    /**
     * Fetch user transactions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $transactions = $request->user()->orders()->orderBy('id', 'DESC')->get();
		return OrderResource::collection($transactions);
    }
}
