<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function getAdvancedStats()
    {
        return response()->json([
            'revenue_by_room_type' => $this->getRevenueByRoomType(),
            'revenue_by_service'   => $this->getRevenueByService(),
            'occupancy_trends'     => $this->getOccupancyTrends(),
            'customer_cities'      => $this->getCustomerCities(),
            'monthly_comparison'   => $this->getMonthlyComparison(),
            'stay_duration'        => $this->getStayDuration(),
        ]);
    }

    private function getStayDuration()
    {
        $avg = Reservation::whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->select(DB::raw('AVG(DATEDIFF(check_out, check_in)) as avg_stay'))
            ->first();
        
        return round($avg->avg_stay ?? 0, 1);
    }

    private function getRevenueByRoomType()
    {
        return DB::table('reservations')
            ->join('rooms', 'reservations.room_id', '=', 'rooms.id')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->whereIn('reservations.status', ['confirmed', 'checked_in', 'checked_out'])
            ->select('room_types.name', DB::raw('SUM(reservations.total_price) as total'))
            ->groupBy('room_types.name')
            ->get();
    }

    private function getRevenueByService()
    {
        return DB::table('reservation_services')
            ->join('services', 'reservation_services.service_id', '=', 'services.id')
            ->select('services.name', DB::raw('SUM(reservation_services.price * reservation_services.quantity) as total'))
            ->groupBy('services.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }

    private function getOccupancyTrends()
    {
        $data = [];
        $totalRooms = Room::where('status', '!=', 'maintenance')->count();
        if ($totalRooms === 0) $totalRooms = 1;

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $occupied = Reservation::whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
                ->where('check_in', '<=', $date)
                ->where('check_out', '>', $date)
                ->count();

            $data[] = [
                'date' => Carbon::parse($date)->format('d M'),
                'rate' => round(($occupied / $totalRooms) * 100, 1)
            ];
        }
        return $data;
    }

    private function getCustomerCities()
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'client'))
            ->whereNotNull('city')
            ->select('city', DB::raw('count(*) as count'))
            ->groupBy('city')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
    }

    private function getMonthlyComparison()
    {
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        
        $lastMonth = now()->subMonth()->month;
        $lastMonthYear = now()->subMonth()->year;

        $currentRevenue = Reservation::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->sum('total_price');

        $lastMonthRevenue = Reservation::whereYear('created_at', $lastMonthYear)
            ->whereMonth('created_at', $lastMonth)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->sum('total_price');

        return [
            'current' => (float)$currentRevenue,
            'last'    => (float)$lastMonthRevenue,
            'growth'  => $lastMonthRevenue > 0 ? round((($currentRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 100
        ];
    }
}
