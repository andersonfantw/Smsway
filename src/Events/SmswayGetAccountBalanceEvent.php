<?php

namespace Cuby\Smsway\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SmswayGetAccountBalanceEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sender;
    public $balance;

    /**
     * Create a new event instance.
     *
     * @param String $balance
     */
    public function __construct(String $sender, String $balance)
    {
        $this->sender = $sender;
        $this->balance = $balance;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('Smsway');
    }
}
