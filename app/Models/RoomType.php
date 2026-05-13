<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description',
        'base_capacity', 'max_capacity',
        'base_price', 'thumbnail', 'is_active'
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'base_price'     => 'float',
        'base_capacity'  => 'integer',
        'max_capacity'   => 'integer',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function dynamicPrices()
    {
        return $this->hasMany(DynamicPrice::class);
    }

    public function getAvailableRoomsCountAttribute(): int
    {
        return $this->rooms()->where('status', 'available')->count();
    }
}