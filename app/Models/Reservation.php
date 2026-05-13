<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'user_id',
        'room_id',
        'promotion_id',
        'check_in',
        'check_out',
        'adults',
        'children',
        'status',
        'room_price',
        'services_price',
        'discount_amount',
        'total_price',
        'special_requests',
        'qr_code',
        'confirmation_token',
        'payment_method',
        'confirmed_at',
        'checked_in_at',
        'checked_out_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'check_in'       => 'date',
        'check_out'      => 'date',
        'confirmed_at'   => 'datetime',
        'checked_in_at'  => 'datetime',
        'checked_out_at' => 'datetime',
        'cancelled_at'   => 'datetime',
        'total_price'    => 'float',
        'room_price'     => 'float',
        'services_price' => 'float',
        'discount_amount'=> 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'reservation_services')
            ->withPivot('quantity', 'price');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function getNightsAttribute(): int
    {
        return $this->check_in->diffInDays($this->check_out);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed'])
            && $this->check_in->isFuture();
    }
}