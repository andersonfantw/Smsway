<?php

namespace Cuby\Smsway\Listeners;

use Cuby\Smsway\Events\SmswaySendSMSEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SmswaySendSMSNotification
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
     * @param  SmswaySendSMSEvent  $event
     * @return void
     */
    public function handle(SmswaySendSMSEvent $event)
    {
        //$this->snsdid
        Log::alert(springf('[%s][%s]%s - %s. %s',datetime('Y-m-d H:i:s'), $event->sender, 'Smsway 簡訊已傳送', sprintf('已傳送簡訊，代碼%s',$event->smsdid), ''));
    }
}
