<x-mail::message>
# Félicitations, {{ $user->name }} ! 🎁

Nous avons le plaisir de vous informer que vous avez effectué 3 réservations ce mois-ci au **Grand Palais**.

Pour vous remercier de votre fidélité exceptionnelle, nous vous offrons une promotion cadeau de **-50%** sur votre prochaine réservation !

<x-mail::panel>
Votre code promo unique : **{{ $promoCode }}**
</x-mail::panel>

*Ce code est valable une seule fois.*

Il vous suffit de saisir ce code lors de votre prochaine réservation pour profiter de votre réduction.

Merci de faire partie de nos clients privilégiés.

Cordialement,<br>
L'équipe du {{ config('app.name') }}
</x-mail::message>
