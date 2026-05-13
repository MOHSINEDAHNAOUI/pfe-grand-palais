<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Mail\ThankYouPostStayMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendPostStayEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-post-stay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send thank you emails to guests 24 hours after their check-out';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $yesterday = Carbon::yesterday()->format('Y-m-d');
        
        $reservations = Reservation::where('status', 'checked_out')
            ->whereDate('check_out', $yesterday)
            ->with('user')
            ->get();

        if ($reservations->isEmpty()) {
            $this->info('No reservations found for check-out date: ' . $yesterday);
            return;
        }

        $this->info('Sending thank you emails to ' . $reservations->count() . ' guests...');

        foreach ($reservations as $reservation) {
            if ($reservation->user && $reservation->user->email) {
                Mail::to($reservation->user->email)->send(new ThankYouPostStayMail($reservation));
                $this->line('Sent to: ' . $reservation->user->email . ' (Ref: ' . $reservation->reference . ')');
            }
        }

        $this->info('All post-stay emails have been sent successfully.');
    }
}
