<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicPrice extends Model
{
    use HasFactory;

    protected $fillable = ['room_type_id', 'date', 'price', 'reason'];

    protected $casts = [
        'price' => 'float',
        'date'  => 'date',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}