<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Session;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\Users\UpdateLastLoggedInAt'
        ]
//        'App\Events\SomeEvent' => [
//            'App\Listeners\EventListener',
//        ],
//        'auth.login' => [
//            'App\Events\AuthHandler@onUserLogin',
//        ]

    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        $events->listen('auth.logout', function($user, $remember) {
            $user->last_session = '123';
            $user->save();
        });
    }
}
