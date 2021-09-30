<?php

namespace Cuby\Meteorsis\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use Cuby\Meteorsis\Events\MeteorsisCallbackEvent;
use Cuby\Meteorsis\Events\MeteorsisGetAccountBalanceEvent;
use Cuby\Meteorsis\Events\MeteorsisGetServerQueryEvent;
use Cuby\Meteorsis\Events\MeteorsisGetSMSStatusEvent;
use Cuby\Meteorsis\Events\MeteorsisSendSMSEvent;
use Cuby\Meteorsis\Listeners\MeteorsisCallbackNotification;
use Cuby\Meteorsis\Listeners\MeteorsisGetAccountBalanceNotification;
use Cuby\Meteorsis\Listeners\MeteorsisGetServerQueryNotification;
use Cuby\Meteorsis\Listeners\MeteorsisGetSMSStatusNotification;
use Cuby\Meteorsis\Listeners\MeteorsisSendSMSNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        MeteorsisCallbackEvent::class => [
            MeteorsisCallbackNotification::class
        ],
        MeteorsisGetAccountBalanceEvent::class => [
            MeteorsisGetAccountBalanceNotification::class
        ],
        MeteorsisGetServerQueryEvent::class => [
            MeteorsisGetServerQueryNotification::class
        ],
        MeteorsisGetSMSStatusEvent::class => [
            MeteorsisGetSMSStatusNotification::class
        ],
        MeteorsisSendSMSEvent::class => [
            MeteorsisSendSMSNotification::class
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
