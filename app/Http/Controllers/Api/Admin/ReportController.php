<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{

    public function occupancy(Request $request)
    {
        $from = $request->get('from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->get('to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $totalRooms = Room::where('status', '!=', 'maintenance')->count();
        $data = [];

        $current = Carbon::parse($from);
        $end     = Carbon::parse($to);

        while ($current->lte($end)) {
            $date    = $current->format('Y-m-d');
            $occupied = Reservation::whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
                ->where('check_in', '<=', $date)
                ->where('check_out', '>', $date)
                ->count();

            $data[] = [
                'date'          => $date,
                'occupied'      => $occupied,
                'total'         => $totalRooms,
                'rate'          => $totalRooms ? round(($occupied / $totalRooms) * 100, 1) : 0,
            ];
            $current->addDay();
        }

        return response()->json([
            'data'        => $data,
            'average_rate' => collect($data)->avg('rate'),
            'period'      => ['from' => $from, 'to' => $to],
        ]);
    }

    public function revenue(Request $request)
    {
        $from = $request->get('from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->get('to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $payments = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$from, $to . ' 23:59:59'])
            ->with('reservation.room.roomType')
            ->get();

        return response()->json([
            'total'            => $payments->sum('amount'),
            'count'            => $payments->count(),
            'average'          => $payments->avg('amount'),
            'by_method'        => $payments->groupBy('method')->map->sum('amount'),
            'by_room_type'     => $payments->groupBy(fn($p) =>
                $p->reservation->room->roomType->name ?? 'N/A'
            )->map->sum('amount'),
            'period'           => ['from' => $from, 'to' => $to],
        ]);
    }

    public function export(Request $request, string $type)
    {
        $format = $request->get('format', 'pdf');

        if ($type === 'reservations') {
            $data = Reservation::with(['user', 'room.roomType', 'payment'])
                ->whereBetween('created_at', [
                    $request->get('from', now()->startOfMonth()),
                    $request->get('to', now()->endOfMonth()),
                ])->get();

            if ($format === 'pdf') {
                $pdf = Pdf::loadView('reports.reservations', ['reservations' => $data]);
                return $pdf->download('reservations-' . now()->format('Y-m') . '.pdf');
            }
        }

        if ($type === 'revenue') {
            $data = Payment::where('status', 'completed')
                ->whereBetween('paid_at', [
                    $request->get('from', now()->startOfMonth()),
                    $request->get('to', now()->endOfMonth()),
                ])->with('reservation.user')->get();

            if ($format === 'pdf') {
                $pdf = Pdf::loadView('reports.revenue', ['payments' => $data]);
                return $pdf->download('revenue-' . now()->format('Y-m') . '.pdf');
            }
        }

        if ($type === 'accounting') {
            $data = Reservation::with(['user', 'room.roomType', 'services', 'payment', 'promotion'])
                ->whereBetween('created_at', [
                    $request->get('from', now()->startOfMonth()),
                    $request->get('to', now()->endOfMonth()),
                ])->orderBy('created_at', 'desc')->get();

            if ($format === 'pdf') {
                $pdf = Pdf::loadView('reports.accounting', [
                    'reservations' => $data,
                    'from' => $request->get('from', now()->startOfMonth()->format('d/m/Y')),
                    'to'   => $request->get('to', now()->endOfMonth()->format('d/m/Y')),
                ]);
                return $pdf->download('rapport-comptable-' . now()->format('Y-m-d') . '.pdf');
            }
        }

        if ($type === 'monthly') {
            $month = $request->get('month', now()->month);
            $year  = $request->get('year', now()->year);
            $from = Carbon::create($year, $month, 1)->startOfMonth();
            $to   = Carbon::create($year, $month, 1)->endOfMonth();

            $data = [
                'monthName'         => $from->translatedFormat('F'),
                'year'              => $year,
                'reservations'      => Reservation::whereBetween('created_at', [$from, $to])->get(),
                'reservationsCount' => Reservation::whereBetween('created_at', [$from, $to])->count(),
                'revenue'           => Payment::where('status', 'completed')
                    ->whereBetween('paid_at', [$from, $to])->sum('amount'),
                'occupancyRate'     => $this->calculateAvgOccupancy($from, $to),
            ];

            if ($format === 'pdf') {
                $pdf = Pdf::loadView('reports.monthly', $data);
                return $pdf->download("rapport-mensuel-{$year}-{$month}.pdf");
            }
        }

        return response()->json(['message' => 'Format ou type non supporté'], 422);
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