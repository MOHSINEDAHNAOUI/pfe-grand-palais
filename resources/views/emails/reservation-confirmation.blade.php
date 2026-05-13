@extends('emails.layout')

@section('title', 'Confirmation de votre Réservation')

@section('content')
  <div style="padding: 40px 0;">
    <div style="text-align: center; margin-bottom: 40px;">
      <h1 style="font-family: 'Georgia', serif; font-size: 32px; font-weight: 300; margin-bottom: 12px; color: #0f172a;">Confirmation de Séjour</h1>
      <p style="font-size: 14px; color: #64748b; letter-spacing: 0.1em; text-transform: uppercase;">Référence : {{ $reservation->reference }}</p>
    </div>

    <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 20px; padding: 40px; margin-bottom: 40px;">
      <p style="font-size: 16px; margin-bottom: 30px; line-height: 1.8;">
        Cher(e) <strong>{{ $reservation->user->name }}</strong>,<br><br>
        Nous avons le plaisir de vous confirmer votre réservation à l'hôtel <strong>Le Grand Palais</strong> de Marrakech. Votre résidence d'exception vous attend.
      </p>

      <table style="width: 100%; border-collapse: collapse; margin-bottom: 40px;">
        <tr>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Chambre</td>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-weight: bold; text-align: right;">{{ $reservation->room->roomType->name }} (N°{{ $reservation->room->number }})</td>
        </tr>
        <tr>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Dates</td>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-weight: bold; text-align: right;">Du {{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m') }} au {{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y') }}</td>
        </tr>
        <tr>
          <td style="padding: 24px 0 0; color: #C9A96E; text-transform: uppercase; font-size: 9px; font-weight: bold;">Montant Total</td>
          <td style="padding: 24px 0 0; color: #0f172a; font-family: 'Georgia', serif; font-size: 24px; font-weight: 300; text-align: right;">{{ number_format($reservation->total_price, 0) }} MAD</td>
        </tr>
      </table>
    </div>

    @if($reservation->qr_code)
    <div style="text-align: center; margin-bottom: 40px; border: 1px dashed #e2e8f0; border-radius: 20px; padding: 30px;">
       <div style="font-size: 9px; font-weight: bold; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 15px;">Scanner au Check-in</div>
       <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($checkInUrl) }}&color=0f172a" 
            alt="Check-in QR" width="120" style="opacity: 0.8; border: 1px solid #f1f5f9; padding: 10px; background: white;">
       <div style="font-size: 11px; color: #64748b; margin-top: 15px;">Référence : {{ $reservation->reference }}</div>
    </div>
    @endif

    <div style="display: table; width: 100%; border-spacing: 12px 0; margin: 0 -12px;">
      <div style="display: table-row;">
        <div style="display: table-cell; width: 50%; background: #ffffff; border: 1px solid #f1f5f9; border-radius: 12px; padding: 20px; vertical-align: top;">
          <div style="font-size: 9px; font-weight: 800; color: #C9A96E; letter-spacing: 0.2em; text-transform: uppercase; margin-bottom: 8px;">Check-in</div>
          <div style="font-size: 11px; color: #64748b;">À partir de 15h00. Réception ouverte 24h/24 pour un accueil d'exception.</div>
        </div>
        <div style="display: table-cell; width: 50%; background: #ffffff; border: 1px solid #f1f5f9; border-radius: 12px; padding: 20px; vertical-align: top;">
          <div style="font-size: 9px; font-weight: 800; color: #C9A96E; letter-spacing: 0.2em; text-transform: uppercase; margin-bottom: 8px;">Check-out</div>
          <div style="font-size: 11px; color: #64748b;">Avant midi. Late check-out disponible sur simple demande à la conciergerie.</div>
        </div>
      </div>
    </div>

  </div>
@endsection
