<?php
namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheckInReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Reservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Rappel : votre arrivée est demain !')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Nous vous rappelons que votre séjour commence demain.')
            ->line('📅 Arrivée : ' . $this->reservation->check_in->format('d/m/Y'))
            ->line('🏨 Chambre : ' . $this->reservation->room->number)
            ->line('📋 Référence : ' . $this->reservation->reference)
            ->action('Voir ma réservation', url('/dashboard/reservations'))
            ->line('Nous vous souhaitons un excellent séjour !');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'reference'      => $this->reservation->reference,
            'check_in'       => $this->reservation->check_in->format('Y-m-d'),
            'type'           => 'check_in_reminder',
        ];
    }
}