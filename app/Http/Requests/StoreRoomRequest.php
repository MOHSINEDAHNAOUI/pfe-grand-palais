<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'room_type_id'   => 'required|exists:room_types,id',
            'number'         => 'required|string|unique:rooms,number',
            'floor'          => 'required|integer|min:0|max:50',
            'surface'        => 'required|numeric|min:5',
            'view'           => 'nullable|string|max:100',
            'smoking'        => 'boolean',
            'status'         => 'in:available,maintenance,blocked',
            'price_override' => 'nullable|numeric|min:0',
            'amenities'      => 'nullable|array',
            'amenities.*'    => 'exists:amenities,id',
            'images'         => 'nullable|array',
            'images.*'       => 'image|mimes:jpeg,png,webp|max:5120',
        ];
    }
}