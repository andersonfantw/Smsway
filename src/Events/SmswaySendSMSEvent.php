<?php

namespace Cuby\Smsway\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SmswaySendSMSEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sender;
    public $smsdid;

    /**
     * Create a new event instance.
     *
     * @param String $sender
     * @param String $smsdid
     */
    public function __construct(String $sender, String $smsdid)
    {
        $this->sender = $sender;
        $this->smsdid = $smsdid;
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
