<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_type_id', 'number', 'floor', 'surface', 'view',
        'smoking', 'status', 'notes', 'price_override'
    ];

    protected $casts = [
        'smoking' => 'boolean',
        'surface' => 'float',
        'price_override' => 'float',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function images()
    {
        return $this->hasMany(RoomImage::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'room_amenities');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function isAvailableFor(string $checkIn, string $checkOut): bool
    {
        return !$this->reservations()
            ->whereIn('status', ['confirmed', 'checked_in', 'pending'])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                          ->where('check_out', '>=', $checkOut);
                    });
            })->exists();
    }

    public function getPriceForDate(string $date): float
    {
        $dynamicPrice = DynamicPrice::where('room_type_id', $this->room_type_id)
            ->where('date', $date)->first();

        if ($dynamicPrice) return $dynamicPrice->price;

        return $this->price_override ?? $this->roomType->base_price;
    }

    public function getPriceForPeriod(string $checkIn, string $checkOut): float
    {
        $total = 0;
        $current = new \DateTime($checkIn);
        $end = new \DateTime($checkOut);

        while ($current < $end) {
            $total += $this->getPriceForDate($current->format('Y-m-d'));
            $current->modify('+1 day');
        }

        return $total;
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }
}