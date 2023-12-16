<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\MpesaDeposit;

class MpesaDepositCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    /**
	 * The mpesa deposit instance.
	 *
	 * @var \App\Models\MpesaDeposit
	 */
	public $deposit;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MpesaDeposit $deposit)
    {
        $this->deposit = $deposit;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
