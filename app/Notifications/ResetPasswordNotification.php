<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as DefaultResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends DefaultResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Define the frontend reset password URL.
        // E.g., http://localhost:5173/reset-password?token=...&email=...
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        $url = $frontendUrl . '/reset-password?token=' . $this->token . '&email=' . urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe - Grand Palais')
            ->view('emails.reset-password', ['url' => $url]);
    }
}
