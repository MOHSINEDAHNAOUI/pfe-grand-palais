<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AIAgentService;
use Twilio\TwiML\MessagingResponse;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    protected $agent;

    public function __construct(AIAgentService $agent)
    {
        $this->agent = $agent;
    }

    /**
     * Webhook pour Twilio WhatsApp
     */
    public function webhook(Request $request)
    {
        $from = $request->input('From'); // Numéro de téléphone du client
        $body = $request->input('Body'); // Message texte

        Log::info("WhatsApp Message from $from: $body");

        // On peut stocker l'historique dans le cache ou la DB plus tard
        // Pour l'instant, on traite le message en direct
        $reply = $this->agent->handleRequest($body, [], $from);

        $response = new MessagingResponse();
        $response->message($reply);

        return response($response, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Test simple pour vérifier que l'agent fonctionne
     */
    public function testAgent(Request $request)
    {
        $message = $request->input('message', 'Bonjour, quelles sont vos chambres disponibles pour ce soir ?');
        return response()->json([
            'user' => $message,
            'agent' => $this->agent->handleRequest($message)
        ]);
    }
}
