<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>REÇU DE SÉJOUR — {{ $reservation->reference }}</title>
    <style>
        @page { 
            margin: 0; 
            size: a4;
        }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            margin: 0; 
            padding: 0;
            color: #1e293b;
            background: #fff;
            line-height: 1.3;
        }
        .container {
            padding: 40px 50px;
            position: relative;
        }
        .top-accent {
            height: 10px;
            background: #0f172a;
            width: 100%;
        }
        .header { 
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 20px;
        }
        .header-left {
            display: table-cell;
            vertical-align: middle;
        }
        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }
        .logo-img {
            width: 150px;
            margin-bottom: 10px;
        }
        .document-title {
            font-family: 'Georgia', serif;
            font-size: 32px;
            color: #0f172a;
            margin: 0;
            letter-spacing: 1px;
        }
        .ref-box {
            text-align: right;
        }
        .ref-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
        }
        .ref-value {
            font-size: 20px;
            font-weight: bold;
            color: #C9A96E;
        }

        .details-section {
            width: 100%;
            margin: 25px 0;
            display: table;
            border-collapse: separate;
            border-spacing: 20px 0;
            margin-left: -20px;
        }
        .details-col {
            display: table-cell;
            background: #f8fafc;
            padding: 15px 20px;
            border-radius: 12px;
            vertical-align: top;
            border: 1px solid #f1f5f9;
        }
        .section-title {
            font-size: 10px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            border-bottom: 2px solid #C9A96E;
            padding-bottom: 3px;
            display: inline-block;
        }
        .info-row { font-size: 11px; margin-bottom: 4px; }
        .info-label { color: #64748b; }
        .info-value { font-weight: bold; color: #0f172a; }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .items-table th {
            text-align: left;
            background: #0f172a;
            color: #fff;
            padding: 10px 15px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .items-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12px;
        }
        .item-desc { font-weight: bold; color: #0f172a; font-size: 13px; }
        .item-sub { font-size: 10px; color: #64748b; }

        .summary-container {
            margin-top: 20px;
            width: 100%;
        }
        .summary-box {
            float: right;
            width: 260px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
        }
        .summary-row {
            display: table;
            width: 100%;
            padding: 4px 0;
        }
        .summary-label { display: table-cell; font-size: 10px; color: #64748b; }
        .summary-value { display: table-cell; text-align: right; font-size: 12px; font-weight: bold; }
        
        .total-row {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #C9A96E;
        }
        .total-label { font-size: 11px; color: #0f172a; font-weight: 900; text-transform: uppercase; }
        .total-amount { font-size: 26px; font-weight: bold; color: #C9A96E; margin-top: 3px; }

        .auth-container {
            margin-top: 50px;
            width: 100%;
            display: table;
        }
        .signature-col {
            display: table-cell;
            width: 65%;
            vertical-align: top;
        }
        .stamp-col {
            display: table-cell;
            width: 35%;
            text-align: right;
            vertical-align: top;
        }
        
        .director-signature {
            margin-top: 5px;
        }
        .signature-graphic {
            height: 80px;
            margin-bottom: -15px;
            margin-left: 10px;
        }
        .signature-line {
            width: 240px;
            border-top: 2px solid #0f172a;
            padding-top: 8px;
            font-size: 10px;
            color: #0f172a;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .director-title { font-size: 10px; color: #C9A96E; font-weight: bold; margin-top: 3px; }

        /* ULTRA-PROFESSIONAL CSS STAMP */
        .professional-stamp {
            display: inline-block;
            width: 140px;
            height: 140px;
            position: relative;
            transform: rotate(-12deg);
        }
        .stamp-ring-outer {
            width: 100%;
            height: 100%;
            border: 4px double #C9A96E;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            padding: 4px;
        }
        .stamp-ring-inner {
            width: 100%;
            height: 100%;
            border: 1px dashed rgba(201, 169, 110, 0.6);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }
        .stamp-text-top {
            position: absolute;
            top: 15px;
            width: 100%;
            font-size: 10px;
            font-weight: 900;
            color: #C9A96E;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .stamp-center-box {
            margin-top: 5px;
        }
        .stamp-emblem {
            font-size: 24px;
            color: #C9A96E;
            line-height: 1;
            margin-bottom: 2px;
        }
        .stamp-valid-label {
            font-size: 8px;
            background: #C9A96E;
            color: #fff;
            padding: 2px 10px;
            font-weight: bold;
            border-radius: 2px;
            text-transform: uppercase;
        }
        .stamp-text-bottom {
            position: absolute;
            bottom: 15px;
            width: 100%;
            font-size: 8px;
            font-weight: bold;
            color: #C9A96E;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .stamp-date-overlay {
            font-size: 7px;
            color: #C9A96E;
            margin-top: 5px;
            font-weight: bold;
        }

        .footer {
            position: absolute;
            bottom: 40px;
            left: 50px;
            right: 50px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="top-accent"></div>
    <div class="container">
        
        <div class="header">
            <div class="header-left">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo-premium.png'))) }}" class="logo-img" alt="Grand Palais Logo" />
                <div class="document-title">Preuve de Séjour Officielle</div>
            </div>
            <div class="header-right">
                <div class="ref-box">
                    <div class="ref-label">Référence Dossier</div>
                    <div class="ref-value">{{ $reservation->reference }}</div>
                    <div class="ref-label" style="margin-top: 8px;">Émis le : {{ date('d/m/Y') }}</div>
                </div>
            </div>
        </div>

        <div class="details-section">
            <div class="details-col">
                <div class="section-title">Le Client</div>
                <div class="info-row"><span class="info-value">{{ $reservation->user->name }}</span></div>
                <div class="info-row"><span class="info-label">E-mail :</span> <span class="info-value">{{ $reservation->user->email }}</span></div>
            </div>
            <div class="details-col">
                <div class="section-title">Le Séjour</div>
                <div class="info-row"><span class="info-label">Dates :</span> <span class="info-value">{{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m') }} au {{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y') }}</span></div>
                <div class="info-row"><span class="info-label">Durée :</span> <span class="info-value">{{ \Carbon\Carbon::parse($reservation->check_in)->diffInDays($reservation->check_out) }} Nuits</span></div>
            </div>
            <div class="details-col">
                <div class="section-title">Paiement</div>
                <div class="info-row"><span class="info-label">Mode :</span> <span class="info-value">{{ $reservation->payment_method === 'card' ? 'Carte Bancaire' : 'Comptoir' }}</span></div>
                <div class="info-row"><span class="info-label">Statut :</span> <span class="info-value" style="color: #16A34A; font-weight: 900;">HONORÉ</span></div>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Désignation des Prestations</th>
                    <th style="text-align: center;">Qté</th>
                    <th style="text-align: right;">Prix Unit.</th>
                    <th style="text-align: right;">Total MAD</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-desc">Hébergement Prestige — Chambre {{ $reservation->room->number }}</div>
                        <div class="item-sub">{{ $reservation->room->room_type?->name }} — Vue {{ $reservation->room->view ?? 'Standard' }}</div>
                    </td>
                    <td style="text-align: center;">{{ \Carbon\Carbon::parse($reservation->check_in)->diffInDays($reservation->check_out) }}</td>
                    <td style="text-align: right;">{{ number_format($reservation->total_price / max(1, \Carbon\Carbon::parse($reservation->check_in)->diffInDays($reservation->check_out)), 0) }}</td>
                    <td style="text-align: right; font-weight: bold; color: #0f172a;">{{ number_format($reservation->total_price, 0) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="summary-container">
            <div class="summary-box">
                <div class="summary-row">
                    <div class="summary-label">Montant Hors Taxes</div>
                    <div class="summary-value">{{ number_format($reservation->total_price * 0.8, 0) }} MAD</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">TVA (20%)</div>
                    <div class="summary-value">{{ number_format($reservation->total_price * 0.2, 0) }} MAD</div>
                </div>
                <div class="total-row">
                    <div class="total-label">Net à Payer TTC</div>
                    <div class="total-amount">{{ number_format($reservation->total_price, 0) }} MAD</div>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="auth-container">
            <div class="signature-col">
                <div class="director-signature">
                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/signature-director.jpg'))) }}" class="signature-graphic" alt="Signature Director" />
                    <div class="signature-line">Directeur Général de l'Établissement</div>
                    <div class="director-title">Grand Palais Marrakech Luxury Hotels Group</div>
                </div>
            </div>
            <div class="stamp-col">
                <div class="professional-stamp">
                    <div class="stamp-ring-outer">
                        <div class="stamp-ring-inner">
                            <div class="stamp-text-top">Grand Palais</div>
                            <div class="stamp-center-box">
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo-premium.png'))) }}" style="width: 45px; margin-bottom: 5px; opacity: 0.8;" alt="Logo" />
                                <div class="stamp-valid-label">Validé</div>
                                <div class="stamp-date-overlay">{{ date('d/m/Y') }}</div>
                            </div>
                            <div class="stamp-text-bottom">Marrakech</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            GRAND PALAIS MARRAKECH — Avenue Mohammed VI, Hivernage, 40000 Marrakech, Maroc<br>
            Tél: +212 5 24 00 00 00 — contact@grandpalais.ma — www.grandpalais.ma<br>
            SARL au capital de 1.000.000 MAD — RC: 12345 — IF: 54321 — ICE: 00123456789000
        </div>
    </div>
</body>
</html>
