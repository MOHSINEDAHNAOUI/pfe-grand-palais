@extends('emails.layout')

@section('title', 'Confirmation de clôture - Grand Palais')

@section('content')
<!-- Hero Section -->
<div class="hero">
  <span class="hero-label">Gestion du Profil</span>
  <h1 class="hero-title">Confirmation de <br>Clôture de Profil</h1>
  <p class="hero-subtitle">Nous avons reçu une demande pour fermer définitivement votre compte résident.</p>
</div>

<!-- Main Content Area -->
<div class="content">
  <p class="text-p">
    Bonjour {{ $user->name }},
  </p>
  
  <p class="text-p">
    Nous tenions à vous confirmer la réception de votre demande de suppression de compte au sein du <strong>Grand Palais</strong>. 
    Cette action est irréversible et entraînera la perte de tout votre historique de séjours, de vos avantages fidélité et de vos préférences de conciergerie.
  </p>

  <div class="premium-card">
    <p style="font-size: 13px; color: #b45309; margin-bottom: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;">Attention</p>
    <p style="font-size: 14px; color: #64748b; line-height: 1.6; margin: 0;">
      Pour valider la suppression, veuillez cliquer sur le bouton ci-dessous. Le lien expirera dans 30 minutes pour votre sécurité.
    </p>
  </div>

  <!-- Call to Action -->
  <div class="btn-container">
    <a href="{{ $url }}" class="btn-gold">Confirmer la suppression</a>
  </div>

  <p class="text-p" style="font-style: italic; font-size: 13px; color: #94a3b8;">
    Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet email. Votre accès restera parfaitement sécurisé.
  </p>

  <p class="text-p">
    Dans l'attente de vous revoir peut-être un jour,<br>
    <strong>La Direction du Grand Palais</strong>
  </p>
</div>
@endsection
