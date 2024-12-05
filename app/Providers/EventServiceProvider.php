<?php

namespace App\Providers;

use App\Events\OrderPubSubEvent;
use App\Events\OrderPubSubListener;
use App\Events\WmsPubSubEvent;
use App\Events\WmsPubSubListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderPubSubEvent::class => [
            OrderPubSubListener::class
        ],
        WmsPubSubEvent::class => [
            WmsPubSubListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
