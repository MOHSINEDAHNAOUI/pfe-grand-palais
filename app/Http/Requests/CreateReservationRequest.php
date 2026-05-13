<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReservationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'room_id'          => 'required|exists:rooms,id',
            'check_in'         => 'required|date|after:today',
            'check_out'        => 'required|date|after:check_in',
            'adults'           => 'required|integer|min:1|max:10',
            'children'         => 'nullable|integer|min:0|max:6',
            'special_requests' => 'nullable|string|max:1000',
            'promo_code'       => 'nullable|string|max:50',
            'services'         => 'nullable|array',
            'services.*.id'    => 'required_with:services|exists:services,id',
            'services.*.quantity' => 'required_with:services|integer|min:1',
            'services.*.price' => 'required_with:services|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'room_id.required'    => 'Veuillez sélectionner une chambre.',
            'room_id.exists'      => 'La chambre sélectionnée n\'existe pas.',
            'check_in.after'      => 'La date d\'arrivée doit être dans le futur.',
            'check_out.after'     => 'La date de départ doit être après la date d\'arrivée.',
            'adults.required'     => 'Le nombre d\'adultes est obligatoire.',
        ];
    }
}