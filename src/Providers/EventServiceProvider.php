<?php

namespace Cuby\Smsway\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use Cuby\Smsway\Events\SmswayCallbackEvent;
use Cuby\Smsway\Events\SmswayGetAccountBalanceEvent;
use Cuby\Smsway\Events\SmswayGetServerQueryEvent;
use Cuby\Smsway\Events\SmswayGetSMSStatusEvent;
use Cuby\Smsway\Events\SmswaySendSMSEvent;
use Cuby\Smsway\Listeners\SmswayCallbackNotification;
use Cuby\Smsway\Listeners\SmswayGetAccountBalanceNotification;
use Cuby\Smsway\Listeners\SmswayGetServerQueryNotification;
use Cuby\Smsway\Listeners\SmswayGetSMSStatusNotification;
use Cuby\Smsway\Listeners\SmswaySendSMSNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SmswayCallbackEvent::class => [
            SmswayCallbackNotification::class
        ],
        SmswayGetAccountBalanceEvent::class => [
            SmswayGetAccountBalanceNotification::class
        ],
        SmswayGetServerQueryEvent::class => [
            SmswayGetServerQueryNotification::class
        ],
        SmswayGetSMSStatusEvent::class => [
            SmswayGetSMSStatusNotification::class
        ],
        SmswaySendSMSEvent::class => [
            SmswaySendSMSNotification::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
