<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
				'receiver_amount' => $this->receiver_amount,
                'receiver_phone_number' => $this->receiver_phone_number,
                'receiver_name' => "",
                'transaction_time' => $this->created_at->format('d-m-Y, H:i'),
                'status' => $this->status,
                'mpesa_status' => $this->credit_status,
				];
    }
}
