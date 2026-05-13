@extends('emails.layout')

@section('title', 'Bienvenue au Grand Palais � MAD� Vérification Email')

@section('content')
  <!-- Hero Section -->
  <div class="hero">
    <div class="hero-label">Bienvenue dans l'exceptionnel</div>
    <div class="hero-title">Prêt pour l'inoubliable ?</div>
    <p class="hero-subtitle">Une dernière étape pour activer votre accès privilège à la collection Grand Palais.</p>
  </div>

  <div class="content">
    <p class="text-p">
      Cher(e) {{ $user->name }},<br><br>
      Nous sommes ravis de vous compter parmi nos membres. Afin de sécuriser votre compte et d'accéder à l'ensemble de nos services de réservation en ligne, merci de valider votre adresse email.
    </p>

    <div class="btn-container">
      <a href="{{ $verificationUrl }}" class="btn-gold">Confirmer mon Adresse</a>
    </div>

    <div style="background-color: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; padding: 24px;">
      <div style="font-size: 9px; font-weight: 800; color: #94a3b8; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 8px;">Sécurité</div>
      <p style="font-size: 11px; color: #64748b; line-height: 1.6; margin: 0;">
        Si vous n'êtes pas à l'origine de cette inscription, vous pouvez ignorer cet email en toute sécurité. Le lien expirera dans 60 minutes.
      </p>
    </div>

    <div style="margin-top: 32px; padding-top: 32px; border-top: 1px dashed #f1f5f9;">
      <p style="font-size: 10px; color: #94a3b8; margin-bottom: 8px;">Si le bouton ne fonctionne pas, copiez ce lien :</p>
      <div style="font-size: 10px; color: #C9A96E; word-break: break-all; font-family: monospace;">{{ $verificationUrl }}</div>
    </div>
  </div>
@endsection
