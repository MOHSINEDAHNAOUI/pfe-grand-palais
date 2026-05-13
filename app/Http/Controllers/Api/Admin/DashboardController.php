<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function overview()
    {
        $user  = auth()->user();
        $today = Carbon::today();

        // Stats chambres
        $data = [
            'today_arrivals'   => Reservation::whereDate('check_in', $today)->whereIn('status', ['confirmed', 'checked_in'])->count(),
            'today_departures' => Reservation::whereDate('check_out', $today)->where('status', 'checked_in')->count(),
            'total_rooms'      => Room::count(),
            'available_rooms'  => Room::where('status', 'available')->count(),
            'occupied_rooms'   => Room::where('status', 'occupied')->count(),
            'maintenance_rooms'=> Room::where('status', 'maintenance')->count(),
            'blocked_rooms'    => Room::where('status', 'blocked')->count(),
            'occupancy_rate'   => $this->getOccupancyRate(),
        ];

        // Stats complètes pour admin et manager seulement
        if ($user->hasRole(['admin', 'manager'])) {
            $data['monthly_revenue']      = Reservation::whereMonth('created_at', now()->month)
                ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
                ->sum('total_price');
            $data['active_reservations']  = Reservation::whereIn('status', ['confirmed', 'checked_in'])->count();
            $data['total_clients']        = User::whereHas('roles', fn($q) => $q->where('name', 'client'))->count();
            $data['pending_reservations'] = Reservation::where('status', 'pending')->count();
        }

        return response()->json($data);
    }

    public function revenueChart(Request $request)
    {
    $period = $request->period ?? '12m';
    $data   = [];

    if ($period === '7j') {
        for ($i = 6; $i >= 0; $i--) {
            $date     = now()->subDays($i);
            $revenue  = Reservation::whereDate('created_at', $date)
                ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
                ->sum('total_price');
            $data[] = ['label' => $date->format('d/m'), 'revenue' => (float) $revenue];
        }
    } elseif ($period === '30j') {
        for ($i = 29; $i >= 0; $i--) {
            $date     = now()->subDays($i);
            $revenue  = Reservation::whereDate('created_at', $date)
                ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
                ->sum('total_price');
            $data[] = ['label' => $date->format('d/m'), 'revenue' => (float) $revenue];
        }
    } else {
        for ($i = 11; $i >= 0; $i--) {
            $date     = now()->subMonths($i);
            $revenue  = Reservation::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
                ->sum('total_price');
            $data[] = ['label' => $date->format('M'), 'revenue' => (float) $revenue];
        }
    }

    return response()->json($data);
    }

    public function reservationsToday()
    {
        $today = Carbon::today();
        $reservations = Reservation::with(['user', 'room.roomType'])
            ->where(fn($q) =>
                $q->whereDate('check_in', $today)
                  ->orWhereDate('check_out', $today)
            )
            ->whereIn('status', ['confirmed', 'checked_in', 'pending'])
            ->orderBy('check_in')
            ->get();

        return response()->json($reservations);
    }

    private function getOccupancyRate(): float
    {
        $total = Room::count();
        if ($total === 0) return 0;
        $occupied = Room::where('status', 'occupied')->count();
        return round(($occupied / $total) * 100, 1);
    }
}