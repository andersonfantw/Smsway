<?php

namespace Cuby\Smsway\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SmswayGetSMSStatusEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sender;
    public $smsdid;
    public $status;
    public $status_text;
    public $errorcode;
    public $errorcode_text;

    /**
     * Create a new event instance.
     *
     * @param String $sender
     * @param String $status
     * @param String $status_text
     * @param String $errorcode
     * @param String $errorcode_text
     */
    public function __construct(String $sender, String $smsdid, String $status, String $status_text, String $errorcode, String $errorcode_text)
    {
        $this->sender = $sender;
        $this->smsdid = $smsdid;
        $this->status = $status;
        $this->status_text = $status_text;
        $this->errorcode = $errorcode;
        $this->errorcode_text = $errorcode_text;
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
