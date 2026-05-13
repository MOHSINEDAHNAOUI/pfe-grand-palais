@extends('emails.layout')

@section('title', 'Merci pour votre séjour')

@section('content')
  <!-- Hero Section -->
  <div class="hero">
    <div class="hero-label">✦ Gratitude ✦</div>
    <div class="hero-title">Merci pour votre confiance</div>
    <p class="hero-subtitle">Ce fut un honneur de vous recevoir au Grand Palais.</p>
  </div>

  <div class="content">
    <p class="text-p">
      Cher(e) {{ $reservation->user->name }},<br><br>
      Nous espérons que vous avez regagné votre domicile en toute sérénité et que les souvenirs de votre séjour parmi nous resteront impérissables. 
      Votre satisfaction est notre plus belle récompense.
    </p>

    <!-- Stay Recap -->
    <div class="premium-card">
      <div style="font-size: 10px; font-weight: 800; letter-spacing: 0.2em; color: #C9A96E; text-transform: uppercase; margin-bottom: 24px; text-align: center;">Souvenir de votre séjour</div>
      
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 13px;">
        <tr>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; text-transform: uppercase; font-size: 9px; font-weight: bold; width: 40%;">Hébergement</td>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-weight: bold; text-align: right;">{{ $reservation->room->roomType->name }} � MAD� Chambre {{ $reservation->room->number }}</td>
        </tr>
        <tr>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; text-transform: uppercase; font-size: 9px; font-weight: bold;">Période</td>
          <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #0f172a; font-weight: bold; text-align: right;">{{ $reservation->check_in->format('d/m/Y') }} � MAD� {{ $reservation->check_out->format('d/m/Y') }}</td>
        </tr>
      </table>
    </div>

    <p class="text-p">
      Votre avis est précieux pour nous et aide notre conciergerie à s'améliorer continuellement. 
      Auriez-vous quelques instants pour partager votre expérience ?
    </p>

    <div class="btn-container">
      <a href="{{ env('FRONTEND_URL', 'http://localhost:5173') }}/dashboard" class="btn-gold">Partager mon avis</a>
    </div>

    <p class="text-p" style="text-align: center; font-size: 11px; color: #94a3b8; margin-top: 40px;">
      Au plaisir de vous revoir très bientôt dans notre établissement.<br>
      L'équipe du Grand Palais
    </p>
  </div>
@endsection
