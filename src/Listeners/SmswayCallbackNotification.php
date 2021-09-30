<?php

namespace Cuby\Smsway\Listeners;

use Cuby\Smsway\Events\SmswayCallbackEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SmswayCallbackNotification
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
     * @param  SmswayCallbackEvent  $event
     * @return void
     */
    public function handle(SmswayCallbackEvent $event)
    {
        //$this->request
        Log::alert(springf('[%s][%s]%s - %s. %s',datetime('Y-m-d H:i:s'), $event->sender, 'Smsway Callback', 'Smsway回傳一個簡訊傳送紀錄', print_r($event->request)));
    }
}
