<?php

namespace App\Listeners\Users;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DateTime;
use Illuminate\Support\Facades\Session;
use Request;

class UpdateLastLoggedInAt
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
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $now = new DateTime;
        $now->setTimezone(new \DateTimeZone("+8"));
        $event->user->updated_at = $now->format('Y-m-d H:i:s');
        $event->user->last_session = Session::getId();
        $event->user->last_used_ip = Request::ip();
        $event->user->save();
    }
}
