<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
	
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
		
        return [
				'id' => $this->id,
				'user_id' => $this->user_id,
                'reference_id' => $this->reference_id,
				'receiver_amount' => number_format($this->receiver_amount, 2),
                'receiver_phone_number' => $this->receiver_phone_number,
                'receiver_name' => "John Wick",
                'transaction_time' => $this->created_at->format('d-m-Y, H:i'),
                'paypal_status' => $this->paypal_status,
                'mpesa_status' => $this->credit_status,
				];
    }
}
