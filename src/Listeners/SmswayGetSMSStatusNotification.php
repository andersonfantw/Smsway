<?php

namespace Cuby\Smsway\Listeners;

use Cuby\Smsway\Events\SmswayGetSMSStatusEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SmswayGetSMSStatusNotification
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
     * @param  SmswayGetSMSStatusEvent  $event
     * @return void
     */
    public function handle(SmswayGetSMSStatusEvent $event)
    {
        //$this->status
        //$this->errorcode
        Log::alert(springf('[%s][%s]%s - %s. %s',
            datetime('Y-m-d H:i:s'),
            $event->sender,
            'Smsway Get SMS Status',
            sprintf('Smsway簡訊代碼%s查詢status[%s] %s, errorcode[%s] %s', $event->smsdid, $event->status, $event->status_text, $event->errorcode, $event->errorcode_text),
            ''));
    }
}
