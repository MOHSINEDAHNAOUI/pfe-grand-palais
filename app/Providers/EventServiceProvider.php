<?php
namespace App\Providers;

use App\Events\ReservationCreated;
use App\Events\ReservationCancelled;
use App\Listeners\SendReservationConfirmation;
use App\Listeners\SendCancellationNotification;
use App\Listeners\SendCheckInReminder;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ReservationCreated::class => [
            SendReservationConfirmation::class,
            SendCheckInReminder::class,
        ],
        ReservationCancelled::class => [
            SendCancellationNotification::class,
        ],
    ];
}