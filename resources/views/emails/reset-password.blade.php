@extends('emails.layout')

@section('header')
Réinitialisation de votre mot de passe
@endsection

@section('content')
<p>Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte <strong>Grand Palais</strong>.</p>

<div class="highlight-box">
    <p style="margin: 0;">Ce lien de réinitialisation expirera dans <strong>15 minutes</strong> pour des raisons de sécurité.</p>
</div>

<div class="button-container">
    <a href="{{ $url }}" class="btn-primary">Réinitialiser mon mot de passe</a>
</div>

<p>Si vous n'avez pas demandé de réinitialisation de mot de passe, aucune autre action n'est requise de votre part. Vos informations restent sécurisées.</p>

<br>
<p>Bien à vous,</p>
<p><strong>L'équipe de conciergerie du Grand Palais</strong></p>
@endsection
