<?php
// ...empty file...

namespace App\Services;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\DynamicPrice;
use App\Models\Reservation;
use Carbon\Carbon;

class DynamicPricingService
{
	public function calculatePrice(RoomType $roomType, string $date): float
	{
		$basePrice = $roomType->base_price;
		$multiplier = 1.0;

		$carbonDate = Carbon::parse($date);

		// Weekend surcharge (+20%)
		if ($carbonDate->isWeekend()) {
			$multiplier += 0.20;
		}

		// Occupation rate impact
		$occupancyRate = $this->getOccupancyRate($date);
		if ($occupancyRate > 0.8) {
			$multiplier += 0.30; // +30% si >80% occupé
		} elseif ($occupancyRate > 0.6) {
			$multiplier += 0.15; // +15% si >60% occupé
		} elseif ($occupancyRate < 0.3) {
			$multiplier -= 0.10; // -10% si <30% occupé
		}

		// Saison haute (été, Noël)
		$month = $carbonDate->month;
		if (in_array($month, [7, 8])) {
			$multiplier += 0.25; // Été +25%
		} elseif (in_array($month, [12])) {
			$multiplier += 0.20; // Décembre +20%
		}

		return round($basePrice * $multiplier, 2);
	}

	public function generatePricesForPeriod(RoomType $roomType, string $start, string $end): void
	{
		$current = Carbon::parse($start);
		$endDate = Carbon::parse($end);

		while ($current->lte($endDate)) {
			$price = $this->calculatePrice($roomType, $current->format('Y-m-d'));

			DynamicPrice::updateOrCreate(
				['room_type_id' => $roomType->id, 'date' => $current->format('Y-m-d')],
				['price' => $price, 'reason' => 'auto']
			);

			$current->addDay();
		}
	}

	private function getOccupancyRate(string $date): float
	{
		$totalRooms = Room::where('status', '!=', 'maintenance')->count();
		if ($totalRooms === 0) return 0;

		$occupiedRooms = Reservation::whereIn('status', ['confirmed', 'checked_in'])
			->where('check_in', '<=', $date)
			->where('check_out', '>', $date)
			->count();

		return $occupiedRooms / $totalRooms;
	}
}
