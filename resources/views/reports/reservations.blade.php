<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport des Réservations</title>
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
        .status-badge {
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-confirmed { background: #e0f2fe; color: #0369a1; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .status-checked_in { background: #dcfce7; color: #166534; }
        
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
            <div class="report-title">Registre des Réservations</div>
            <div class="period-badge">Période Exportée</div>
        </div>
    </div>

    <h3>Détail des Réservations</h3>
    <table>
        <thead>
            <tr>
                <th>Référence</th>
                <th>Client</th>
                <th>Chambre</th>
                <th>Arrivée</th>
                <th>Départ</th>
                <th>Statut</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservations as $r)
            <tr>
                <td style="font-weight: bold; color: #C9A96E;">{{ $r->reference }}</td>
                <td>{{ $r->user->name }}<br><span style="font-size: 8px; color: #64748b;">{{ $r->user->email }}</span></td>
                <td>{{ $r->room->roomType?->name }}<br><span style="font-size: 8px; color: #64748b;">Chambre {{ $r->room->number }}</span></td>
                <td>{{ $r->check_in->format('d/m/Y') }}</td>
                <td>{{ $r->check_out->format('d/m/Y') }}</td>
                <td>
                    <span class="status-badge status-{{ $r->status }}">
                        {{ $r->status }}
                    </span>
                </td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($r->total_price, 0) }} MAD</td>
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
