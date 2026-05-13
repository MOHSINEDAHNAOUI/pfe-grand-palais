<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Reservation;

class ReservationPolicy
{
    public function view(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id
            || $user->hasAnyRole(['admin', 'manager', 'receptionist']);
    }

    public function cancel(User $user, Reservation $reservation): bool
    {
        return ($user->id === $reservation->user_id || $user->hasAnyRole(['admin', 'manager']))
            && $reservation->canBeCancelled();
    }

    public function pay(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id
            && $reservation->status === 'pending';
    }

    public function review(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id
            && $reservation->status === 'checked_out'
            && !$reservation->review;
    }
}