<?php
namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Reservation $reservation) {}

    public function via(object $notifiable): array { return ['mail']; }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre avis compte pour nous !')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Merci d\'avoir séjourné chez nous.')
            ->line('Votre avis nous aide à améliorer nos services.')
            ->action('Laisser un avis', url('/dashboard/reservations/' . $this->reservation->id . '/review'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'type'           => 'review_request',
        ];
    }
}