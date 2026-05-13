<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price',
        'price_type', 'category', 'icon', 'image', 'is_active'
    ];

    protected $casts = [
        'price'     => 'float',
        'is_active' => 'boolean',
    ];

    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_services')
            ->withPivot('quantity', 'price');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}