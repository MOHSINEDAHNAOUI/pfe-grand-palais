@extends('emails.layout')

@section('title', 'Nouvelle Réservation Reçue')

@section('content')
  <div style="padding: 40px 0;">
    <div style="text-align: center; margin-bottom: 40px;">
      <h1 style="font-family: 'Georgia', serif; font-size: 28px; font-weight: 300; margin-bottom: 12px; color: #0f172a;">Alerte Réservation</h1>
      <p style="font-size: 14px; color: #C9A96E; letter-spacing: 0.1em; text-transform: uppercase;">Un nouveau séjour a été réservé</p>
    </div>

    <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 20px; padding: 40px; margin-bottom: 40px;">
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 40px;">
        <tr>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 11px; text-transform: uppercase;">Client</td>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-weight: bold; text-align: right;">{{ $reservation->user->name }} ({{ $reservation->user->email }})</td>
        </tr>
        <tr>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 11px; text-transform: uppercase;">Chambre</td>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-weight: bold; text-align: right;">{{ $reservation->room->roomType->name }} (N°{{ $reservation->room->number }})</td>
        </tr>
        <tr>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 11px; text-transform: uppercase;">Dates</td>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-weight: bold; text-align: right;">Du {{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m') }} au {{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y') }}</td>
        </tr>
        <tr>
          <td style="padding: 24px 0 0; color: #C9A96E; text-transform: uppercase; font-size: 9px; font-weight: bold;">Montant Total</td>
          <td style="padding: 24px 0 0; color: #0f172a; font-family: 'Georgia', serif; font-size: 24px; font-weight: 300; text-align: right;">{{ number_format($reservation->total_price, 0) }} MAD</td>
        </tr>
      </table>

      <div style="text-align: center;">
        <a href="{{ env('FRONTEND_URL', 'http://localhost:5173') }}/admin/reservations" 
           style="display: inline-block; background-color: #0f172a; color: #ffffff; padding: 16px 32px; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em;">
          Voir dans l'interface Admin
        </a>
      </div>
    </div>

  </div>
@endsection
