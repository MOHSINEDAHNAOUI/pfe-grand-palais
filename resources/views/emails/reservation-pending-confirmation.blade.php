@extends('emails.layout')

@section('title', 'Finalisez votre Réservation')

@section('content')
  <div style="padding: 40px 0;">
    <div style="text-align: center; margin-bottom: 40px;">
      <h1 style="font-family: 'Georgia', serif; font-size: 32px; font-weight: 300; margin-bottom: 12px; color: #0f172a;">Presque terminé...</h1>
      <p style="font-size: 14px; color: #64748b; letter-spacing: 0.1em; text-transform: uppercase;">Référence : {{ $reservation->reference }}</p>
    </div>

    <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 20px; padding: 40px; margin-bottom: 32px;">
      <p style="font-size: 16px; margin-bottom: 30px; line-height: 1.8;">
        Cher(e) <strong>{{ $reservation->user->name }}</strong>,<br><br>
        Nous avons bien reçu votre demande de réservation. Pour garantir votre séjour au <strong>Grand Palais Marrakech</strong>, nous vous prions de confirmer votre arrivée en cliquant sur le bouton ci-dessous.
      </p>

      <table style="width: 100%; border-collapse: collapse; margin-bottom: 40px;">
        <tr>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Hébergement</td>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-weight: bold; text-align: right;">{{ $reservation->room->roomType->name }}</td>
        </tr>
        <tr>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Dates</td>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-weight: bold; text-align: right;">{{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m') }} — {{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y') }}</td>
        </tr>
        <tr>
          <td style="padding: 24px 0 0; color: #C9A96E; text-transform: uppercase; font-size: 9px; font-weight: bold;">Montant Estimé</td>
          <td style="padding: 24px 0 0; color: #0f172a; font-family: 'Georgia', serif; font-size: 24px; font-weight: 300; text-align: right;">{{ number_format($reservation->total_price, 0) }} MAD</td>
        </tr>
      </table>
    </div>

    <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 20px; border-radius: 8px; margin-bottom: 32px;">
      <p style="font-size: 12px; color: #92400e; margin: 0;">
        Attention : Ce lien est valable pendant <strong>24 heures</strong>. Passé ce délai, la chambre sera remise en disponibilité pour les autres voyageurs.
      </p>
    </div>

    <div style="text-align: center; margin-bottom: 40px;">
      <a href="{{ env('FRONTEND_URL', 'http://localhost:5173') }}/booking/confirm-arrival/{{ $reservation->id }}?token={{ $reservation->confirmation_token }}" 
         style="display: inline-block; background-color: #0f172a; color: #ffffff; padding: 18px 36px; border-radius: 12px; text-decoration: none; font-weight: bold; font-size: 14px; text-transform: uppercase; letter-spacing: 0.1em;">
        Confirmer ma réservation
      </a>
    </div>

  </div>
@endsection
