@extends('emails.layout')

@section('title', 'Annulation de votre séjour � MAD� Grand Palais')

@section('content')
  <!-- Hero Section -->
  <div class="hero">
    <div class="hero-label">Annulation confirmée</div>
    <div class="hero-title">Ce n'est qu'un au revoir</div>
    <p class="hero-subtitle">Votre réservation a été annulée. Nous espérons avoir le plaisir de vous accueillir très prochainement au Palais.</p>
  </div>

  <div class="content">
    <p class="text-p">
      Bonjour {{ $reservation->user->name }},<br><br>
      Nous vous confirmons l'annulation de votre dossier. Aucun autre frais ne sera débité sur votre mode de règlement pour ce séjour.
    </p>

    <!-- Essential Recap -->
    <div class="premium-card">
      <div style="font-size: 10px; font-weight: 800; letter-spacing: 0.2em; color: #94a3b8; text-transform: uppercase; margin-bottom: 24px; text-align: center;">Détails du dossier annulé</div>
      
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 13px;">
        <tr>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; text-transform: uppercase; font-size: 9px; font-weight: bold; width: 40%;">Référence</td>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-family: monospace; font-weight: bold; text-align: right;">{{ $reservation->reference }}</td>
        </tr>
        <tr>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; text-transform: uppercase; font-size: 9px; font-weight: bold;">Chambre</td>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-weight: bold; text-align: right;">N° {{ $reservation->room->number }}</td>
        </tr>
        <tr>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; text-transform: uppercase; font-size: 9px; font-weight: bold;">Dates prévues</td>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-weight: bold; text-align: right;">
            {{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m') }} � MAD� {{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y') }}
          </td>
        </tr>
        @if($reservation->cancellation_reason)
        <tr>
          <td style="padding: 12px 0; color: #64748b; text-transform: uppercase; font-size: 9px; font-weight: bold;">Raison</td>
          <td style="padding: 12px 0; color: #e11d48; font-size: 11px; text-align: right; italic">{{ $reservation->cancellation_reason }}</td>
        </tr>
        @endif
      </table>
    </div>

    <div class="btn-container">
      <a href="{{ env('FRONTEND_URL', 'http://localhost:5173') }}/rooms" class="btn-gold">Explorer nos autres Joyaux</a>
    </div>

    <p style="text-align: center; font-size: 12px; color: #94a3b8; font-style: italic;">
      Si vous n'êtes pas à l'origine de cette annulation, veuillez contacter d'urgence notre conciergerie.
    </p>

  </div>
@endsection
