<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReservationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $reservation;

    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('✨ Nouvelle Réservation — ' . $this->reservation->reference)
            ->view('emails.admin-new-reservation', [
                'reservation' => $this->reservation
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'reference'      => $this->reservation->reference,
            'user_name'      => $this->reservation->user->name,
            'total_price'    => $this->reservation->total_price,
            'message'        => 'Nouvelle réservation reçue : ' . $this->reservation->reference,
            'type'           => 'new_reservation'
        ];
    }
}
