<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport Comptable Détaillé</title>
    <style>
        @page { 
            margin: 30px; 
            size: A4 landscape;
        }
        body { 
            font-family: 'Helvetica', sans-serif; 
            color: #0f172a; 
            margin: 0; 
            padding: 20px;
            background: #fff;
            font-size: 9px;
        }
        .header { 
            border-bottom: 2px solid #C9A96E; 
            padding-bottom: 15px; 
            margin-bottom: 20px; 
            display: table;
            width: 100%;
        }
        .header-left { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .logo-img { width: 80px; }
        .report-title { font-size: 20px; font-weight: bold; margin: 0; color: #0f172a; }
        .period-info { font-size: 10px; color: #64748b; margin-top: 5px; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: fixed; }
        th { 
            background: #0f172a; 
            color: #fff; 
            padding: 8px 4px; 
            text-align: left; 
            text-transform: uppercase; 
            font-size: 7px; 
            letter-spacing: 0.5px;
            border: 1px solid #1e293b;
        }
        td { 
            padding: 6px 4px; 
            border: 1px solid #e2e8f0; 
            word-wrap: break-word;
            vertical-align: top;
        }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-accent { color: #C9A96E; }
        
        .footer { 
            margin-top: 30px; 
            text-align: center; 
            font-size: 8px; 
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }
        .summary-box {
            margin-top: 20px;
            float: right;
            width: 250px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }
        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .summary-label { display: table-cell; font-weight: bold; }
        .summary-value { display: table-cell; text-align: right; font-weight: bold; color: #0f172a; }
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
            <div class="report-title">Grand Palais - Journal Comptable Détaillé</div>
            <div class="period-info">Période du {{ $from }} au {{ $to }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="8%">Date Opé.</th>
                <th width="8%">Référence</th>
                <th width="12%">Client</th>
                <th width="10%">Chambre / Type</th>
                <th width="12%">Période Séjour</th>
                <th width="5%">Nuits</th>
                <th width="8%">Base (Chambre)</th>
                <th width="8%">Services</th>
                <th width="8%">Remise</th>
                <th width="8%">Total TTC</th>
                <th width="8%">Paiement</th>
                <th width="7%">Statut</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalBase = 0; 
                $totalServices = 0; 
                $totalDiscounts = 0; 
                $totalFinal = 0; 
            @endphp
            @foreach($reservations as $r)
            @php
                $totalBase += $r->room_price;
                $totalServices += $r->services_price;
                $totalDiscounts += $r->discount_amount;
                $totalFinal += $r->total_price;
            @endphp
            <tr>
                <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                <td class="font-bold text-accent">{{ $r->reference }}</td>
                <td>
                    {{ $r->user->name }}<br>
                    <span style="font-size: 7px; color: #64748b;">{{ $r->user->email }}</span>
                </td>
                <td>
                    № {{ $r->room->number }}<br>
                    <span style="font-size: 7px; color: #64748b;">{{ $r->room->roomType?->name }}</span>
                </td>
                <td>{{ \Carbon\Carbon::parse($r->check_in)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($r->check_out)->format('d/m/Y') }}</td>
                <td class="text-right">{{ \Carbon\Carbon::parse($r->check_in)->diffInDays($r->check_out) }}</td>
                <td class="text-right">{{ number_format($r->room_price, 2) }}</td>
                <td class="text-right">{{ number_format($r->services_price, 2) }}</td>
                <td class="text-right text-accent">{{ number_format($r->discount_amount, 2) }}</td>
                <td class="text-right font-bold">{{ number_format($r->total_price, 2) }}</td>
                <td>
                    @if($r->payment)
                        {{ strtoupper($r->payment->method) }}<br>
                        <span style="font-size: 7px; color: #64748b;">{{ $r->payment->paid_at ? \Carbon\Carbon::parse($r->payment->paid_at)->format('d/m/Y') : '-' }}</span>
                    @else
                        -
                    @endif
                </td>
                <td>{{ strtoupper($r->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <div class="summary-row">
            <span class="summary-label">Total Base Hébergement :</span>
            <span class="summary-value">{{ number_format($totalBase, 2) }} MAD</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Prestations Annexes :</span>
            <span class="summary-value">{{ number_format($totalServices, 2) }} MAD</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Remises Accordées :</span>
            <span class="summary-value">- {{ number_format($totalDiscounts, 2) }} MAD</span>
        </div>
        <div style="border-top: 1px solid #cbd5e1; margin: 10px 0;"></div>
        <div class="summary-row">
            <span class="summary-label" style="font-size: 14px;">CHIFFRE D'AFFAIRES TOTAL :</span>
            <span class="summary-value" style="font-size: 14px; color: #b45309;">{{ number_format($totalFinal, 2) }} MAD</span>
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        GRAND PALAIS MARRAKECH — Direction Administrative et Financière<br>
        Document confidentiel à usage comptable — Généré le {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
