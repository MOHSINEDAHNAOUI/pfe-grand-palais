<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\AnnouncementMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AnnouncementController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
            'type'    => 'required|in:promotion,event,info',
            'action_label' => 'nullable|string|max:50',
            'action_url'   => 'nullable|url',
        ]);

        // On récupère tous les clients actifs
        $clients = User::role('client', 'web')->where('is_active', true)->get();

        if ($clients->isEmpty()) {
            return response()->json(['message' => 'Aucun client actif trouvé.'], 404);
        }

        // Pour un vrai projet, on utiliserait une queue (Job)
        // Ici on simule ou on envoie directement si peu de clients
        foreach ($clients as $client) {
            Mail::to($client->email)->send(new AnnouncementMail(
                $request->title,
                $request->message,
                $request->action_label,
                $request->action_url
            ));
        }

        return response()->json(['message' => 'Annonce envoyée avec succès à ' . $clients->count() . ' clients.']);
    }
}
