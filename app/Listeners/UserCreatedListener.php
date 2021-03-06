<?php

namespace App\Listeners;

use GuzzleHttp\retry;
use App\Mail\UserCreated;
use App\Events\UserCreatedEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserCreatedListener
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
     * @param  UserCreatedEvent  $event
     * @return void
     */
    public function handle(UserCreatedEvent $event)
    {
        retry(5, function() use($event){
            Mail::to($event->user)->send(new UserCreated($event->user));
        },100);
    }
}
