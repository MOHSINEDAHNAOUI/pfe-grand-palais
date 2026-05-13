<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'room_id', 'reservation_id',
        'rating', 'cleanliness_rating', 'comfort_rating',
        'service_rating', 'comment', 'is_approved'
    ];

    protected $casts = [
        'is_approved'        => 'boolean',
        'rating'             => 'integer',
        'cleanliness_rating' => 'integer',
        'comfort_rating'     => 'integer',
        'service_rating'     => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function getAverageRatingAttribute(): float
    {
        $ratings = array_filter([
            $this->rating,
            $this->cleanliness_rating,
            $this->comfort_rating,
            $this->service_rating,
        ]);
        return count($ratings) ? round(array_sum($ratings) / count($ratings), 1) : 0;
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}