<?php

namespace Cuby\Smsway\Events;

use Illuminate\Http\Request;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SmswayCallbackEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sender;
    public $request;

    /**
     * Create a new event instance.
     *
     * @param String $sender
     * @param Request $request
     */
    public function __construct(String $sender, Request $request)
    {
        $this->sender = $sender;
        $this->request = $request;
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
