<?php

namespace App\Services;

use App\Models\Room;
use App\Models\User;
use App\Models\RoomType;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AIAgentService
{
    protected $client;
    protected $model = 'llama-3.3-70b-versatile'; 

    public function __construct()
    {
        $apiKey = env('GROQ_API_KEY');
        $baseUrl = 'https://api.groq.com/openai/v1';

        $this->client = \OpenAI::factory()
            ->withApiKey($apiKey)
            ->withBaseUri($baseUrl)
            ->make();
    }

    public function handleRequest($message, $history = [], $from = null)
    {
        $systemPrompt = "Tu es l'agent concierge virtuel de l'hôtel 'Le Grand Palais' situé à Marrakech, au Maroc. 
        Ton but est d'aider les clients à réserver des chambres et répondre à leurs questions sur nos services de luxe.
        Tu dois TOUJOURS répondre dans la langue utilisée par le client (Arabe, Français ou Anglais).
        Important : Tous les prix indiqués doivent être en Dirhams Marocains (MAD).
        1. Demande les dates (arrivée/départ) et le nombre de personnes.
        2. Vérifie la disponibilité avec 'get_available_rooms'.
        3. Propose les chambres avec leurs prix en MAD.
        4. Une fois la chambre choisie, demande IMPÉRATIVEMENT le NOM COMPLET et l'ADRESSE EMAIL du client.
        5. Utilise 'create_reservation' avec ces informations.
        6. Après la création, informe le client que : 'La confirmation de votre réservation se fera à votre arrivée à l'hôtel à Marrakech'.
        
        Sois toujours poli, chaleureux (hospitalité marocaine) et luxueux. 
        Note: Aujourd'hui nous sommes le " . date('Y-m-d') . ".";

        $formattedHistory = array_map(fn($m) => [
            'role' => $m['role'],
            'content' => $m['content'] ?? ''
        ], $history);

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $formattedHistory,
            [['role' => 'user', 'content' => $message]]
        );

        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_available_rooms',
                    'description' => 'Vérifie quelles chambres sont disponibles pour des dates précises.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'check_in' => ['type' => 'string', 'description' => 'Date d\'arrivée (YYYY-MM-DD)'],
                            'check_out' => ['type' => 'string', 'description' => 'Date de départ (YYYY-MM-DD)'],
                        ],
                        'required' => ['check_in', 'check_out'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_hotel_services',
                    'description' => 'Donne la liste des services disponibles (Spa, Restaurant, etc.).',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => new \stdClass(),
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'create_reservation',
                    'description' => 'Crée une réservation officielle dans la base de données.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'room_number' => ['type' => 'integer', 'description' => 'Numéro de la chambre choisie'],
                            'check_in' => ['type' => 'string', 'description' => 'Date d\'arrivée (YYYY-MM-DD)'],
                            'check_out' => ['type' => 'string', 'description' => 'Date de départ (YYYY-MM-DD)'],
                            'guest_name' => ['type' => 'string', 'description' => 'Nom complet du client'],
                            'guest_email' => ['type' => 'string', 'description' => 'Adresse email réelle du client'],
                            'adults' => ['type' => 'integer', 'description' => 'Nombre d\'adultes'],
                            'children' => ['type' => 'integer', 'description' => 'Nombre d\'enfants'],
                        ],
                        'required' => ['room_number', 'check_in', 'check_out', 'guest_name', 'guest_email', 'adults'],
                    ],
                ],
            ]
        ];

        try {
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => $messages,
                'tools' => $tools,
                'tool_choice' => 'auto',
            ]);

            $messageResponse = $response->choices[0]->message;

            if ($messageResponse->toolCalls) {
                $messages[] = [
                    'role' => 'assistant',
                    'content' => $messageResponse->content,
                    'tool_calls' => array_map(fn($tc) => [
                        'id' => $tc->id,
                        'type' => 'function',
                        'function' => [
                            'name' => $tc->function->name,
                            'arguments' => $tc->function->arguments,
                        ]
                    ], $messageResponse->toolCalls)
                ];

                foreach ($messageResponse->toolCalls as $toolCall) {
                    $functionName = $toolCall->function->name;
                    $arguments = json_decode($toolCall->function->arguments, true);
                    $result = $this->callTool($functionName, $arguments, $from);

                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $toolCall->id,
                        'name' => $functionName,
                        'content' => json_encode($result),
                    ];
                }

                $finalResponse = $this->client->chat()->create([
                    'model' => $this->model,
                    'messages' => $messages,
                ]);

                return $finalResponse->choices[0]->message->content;
            }

            return $messageResponse->content;

        } catch (\Exception $e) {
            Log::error("AIAgent Error: " . $e->getMessage());
            return "Désolé, je rencontre une petite difficulté technique. Puis-je vous aider autrement ?";
        }
    }

    protected function callTool($name, $args, $from = null)
    {
        switch ($name) {
            case 'get_available_rooms':
                $checkIn = $args['check_in'];
                $checkOut = $args['check_out'];
                
                $rooms = Room::with('roomType')->get();
                $available = $rooms->filter(fn($r) => $r->isAvailableFor($checkIn, $checkOut));
                
                return $available->map(fn($r) => [
                    'number' => $r->number,
                    'type' => $r->roomType->name,
                    'price' => $r->getPriceForDate($checkIn),
                ])->values()->all();

            case 'get_hotel_services':
                return \App\Models\Service::active()->get(['name', 'description', 'price']);

            case 'create_reservation':
                return $this->processReservation($args, $from);

            default:
                return "Outil non trouvé.";
        }
    }

    protected function processReservation($args, $from)
    {
        try {
            $room = Room::where('number', $args['room_number'])->first();
            if (!$room) return "Chambre introuvable.";

            $phone = $from ? str_replace('whatsapp:', '', $from) : '0000000000';

            // Chercher l'utilisateur par email d'abord, sinon par téléphone
            $user = User::where('email', $args['guest_email'])->first();
            if (!$user) {
                $user = User::where('phone', $phone)->first();
            }
            
            if (!$user) {
                $user = User::create([
                    'name' => $args['guest_name'],
                    'email' => $args['guest_email'],
                    'phone' => $phone,
                    'password' => bcrypt(Str::random(12)),
                    'role' => 'client'
                ]);
            }

            $checkIn = $args['check_in'];
            $checkOut = $args['check_out'];
            $roomPrice = $room->getPriceForPeriod($checkIn, $checkOut);

            $reservation = Reservation::create([
                'reference' => 'RES-' . strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'room_id' => $room->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'adults' => $args['adults'],
                'children' => $args['children'] ?? 0,
                'status' => 'pending',
                'room_price' => $roomPrice,
                'total_price' => $roomPrice,
                'payment_method' => 'on_arrival',
            ]);

            return [
                'status' => 'success',
                'reference' => $reservation->reference,
                'message' => "Réservation créée pour " . $args['guest_name'] . " (" . $args['guest_email'] . ")."
            ];

        } catch (\Exception $e) {
            Log::error("Reservation Error: " . $e->getMessage());
            return "Erreur lors de la réservation.";
        }
    }
}
