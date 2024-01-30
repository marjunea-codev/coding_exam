<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\StripeTrait;
use App\Models\User;

class UserRegisteredListener
{
    use StripeTrait;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;

        $this->connectToStripe();

        $customer = $this->createStripeCustomer( $user );

        if( $customer ){
            $user->stripe_id = $customer->id;
            $user->save();
        }
    }
}
