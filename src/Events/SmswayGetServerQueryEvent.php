<?php

namespace Cuby\Smsway\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SmswayGetServerQueryEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sender;
    public $queue;

    /**
     * Create a new event instance.
     *
     * @param String $sender
     * @param String $queue
     */
    public function __construct(String $sender, String $queue)
    {
        $this->sender = $sender;
        $this->queue = $queue;
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
