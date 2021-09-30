<?php

namespace Cuby\Smsway\Listeners;

use Cuby\Smsway\Events\SmswayGetServerQueryEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SmswayGetServerQueryNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SmswayGetServerQueryEvent  $event
     * @return void
     */
    public function handle(SmswayGetServerQueryEvent $event)
    {
        //$this->queue
        Log::alert(springf('[%s][%s]%s - %s. %s',datetime('Y-m-d H:i:s'), $event->sender, 'Smsway Get Server Query', sprintf('Smsway簡訊截至目前有 %s 個排程',$event->queue), ''));
    }
}
