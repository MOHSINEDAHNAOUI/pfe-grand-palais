<?php
namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ThankYouPostStayMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⭐ Merci pour votre séjour au Grand Palais !',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.thank-you-post-stay',
            with: [
                'reviewUrl' => env('FRONTEND_URL', 'http://localhost:5173') . '/rooms/' . $this->reservation->room_id . '#reviews',
            ]
        );
    }
}
