<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport des Revenus</title>
    <style>
        @page { margin: 0; }
        body { 
            font-family: 'Helvetica', sans-serif; 
            color: #0f172a; 
            margin: 0; 
            padding: 50px;
            background: #fff;
        }
        .header { 
            border-bottom: 4px solid #C9A96E; 
            padding-bottom: 30px; 
            margin-bottom: 40px; 
            display: table;
            width: 100%;
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
            width: 120px;
        }
        .report-title { 
            font-size: 28px; 
            font-weight: 300; 
            font-style: italic; 
            margin: 0;
            color: #0f172a;
        }
        .period-badge {
            display: inline-block;
            background: #0f172a;
            color: #C9A96E;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 10px;
        }
        
        .total-banner {
            background: #f8fafc;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 40px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        .total-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #64748b;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .total-amount {
            font-size: 36px;
            font-weight: bold;
            color: #C9A96E;
        }

        h3 { 
            font-family: 'Georgia', serif;
            font-size: 18px; 
            margin-bottom: 20px; 
            color: #0f172a;
            border-left: 5px solid #0f172a;
            padding-left: 15px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th { 
            text-align: left; 
            padding: 12px 15px; 
            background: #0f172a; 
            color: #fff;
            font-size: 9px; 
            text-transform: uppercase; 
            letter-spacing: 1px;
        }
        td { 
            padding: 12px 15px; 
            border-bottom: 1px solid #f1f5f9; 
            font-size: 11px; 
        }
        
        .footer { 
            margin-top: 50px; 
            text-align: center; 
            font-size: 9px; 
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            @if(file_exists(public_path('images/logo-premium.png')))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo-premium.png'))) }}" class="logo-img" alt="Logo" />
            @endif
        </div>
        <div class="header-right">
            <div class="report-title">Journal des Encaissements</div>
            <div class="period-badge">Comptabilité</div>
        </div>
    </div>

    <div class="total-banner">
        <div class="total-label">Revenu Total sur la Période</div>
        <div class="total-amount">{{ number_format($payments->sum('amount'), 0) }} MAD</div>
    </div>

    <h3>Détail des Transactions</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Référence</th>
                <th>Client</th>
                <th>Méthode</th>
                <th style="text-align: right;">Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $p)
            <tr>
                <td>{{ \Carbon\Carbon::parse($p->paid_at)->format('d/m/Y H:i') }}</td>
                <td style="font-weight: bold; color: #C9A96E;">{{ $p->reservation?->reference }}</td>
                <td>{{ $p->reservation?->user?->name }}</td>
                <td style="text-transform: uppercase; font-size: 9px;">{{ $p->method }}</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($p->amount, 0) }} MAD</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        GRAND PALAIS MARRAKECH — Direction Administrative et Financière<br>
        Document confidentiel généré le {{ date('d/m/Y H:i') }} par le Système Central
    </div>
</body>
</html>
