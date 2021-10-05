<?php

namespace Cuby\Smsway\Listeners;

use Cuby\Smsway\Events\SmswayGetAccountBalanceEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SmswayGetAccountBalanceNotification
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
     * @param  SmswayGetAccountBalanceEvent  $event
     * @return void
     */
    public function handle(SmswayGetAccountBalanceEvent $event)
    {
        //$event->balance
        Log::alert(sprintf('[%s][%s]%s - %s. %s',date('Y-m-d H:i:s'), $event->sender, 'Smsway Get Account Balance', sprintf('Smsway截至今日的帳戶餘額為 %s',$event->balance), ''));
    }
}
