<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Room;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class GenerateMonthlyReport extends Command
{
    protected $signature   = 'report:generate {--month=} {--year=}';
    protected $description = 'Générer le rapport mensuel PDF';

    public function handle(): void
    {
        $month = $this->option('month') ?? Carbon::now()->subMonth()->month;
        $year  = $this->option('year')  ?? Carbon::now()->subMonth()->year;

        $from = Carbon::create($year, $month, 1)->startOfMonth();
        $to   = Carbon::create($year, $month, 1)->endOfMonth();

        $this->info("Génération du rapport pour {$from->format('F Y')}...");

        $data = [
            'monthName'         => $from->translatedFormat('F'),
            'year'              => $year,
            'reservations'      => Reservation::whereBetween('created_at', [$from, $to])->get(),
            'reservationsCount' => Reservation::whereBetween('created_at', [$from, $to])->count(),
            'revenue'           => Payment::where('status', 'completed')
                ->whereBetween('paid_at', [$from, $to])->sum('amount'),
            'occupancyRate'     => $this->calculateAvgOccupancy($from, $to),
        ];

        $pdf  = Pdf::loadView('reports.monthly', $data);
        $path = "reports/monthly-{$year}-{$month}.pdf";
        Storage::put('public/' . $path, $pdf->output());

        $this->info("✓ Rapport sauvegardé : storage/app/public/{$path}");
    }

    private function calculateAvgOccupancy(Carbon $from, Carbon $to): float
    {
        $totalRooms = Room::where('status', '!=', 'maintenance')->count();
        if (!$totalRooms) return 0;

        $days  = $from->diffInDays($to) + 1;
        $total = 0;

        $current = $from->clone();
        while ($current->lte($to)) {
            $date = $current->format('Y-m-d');
            $occupied = Reservation::whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
                ->where('check_in', '<=', $date)->where('check_out', '>', $date)->count();
            $total += $occupied / $totalRooms;
            $current->addDay();
        }

        return round(($total / $days) * 100, 1);
    }
}