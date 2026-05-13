<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport Mensuel - {{ $monthName }} {{ $year }}</title>
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

        .stat-grid { 
            width: 100%; 
            margin-bottom: 40px; 
            border-spacing: 15px 0;
            margin-left: -15px;
            margin-right: -15px;
        }
        .stat-card { 
            background: #f8fafc; 
            padding: 25px 15px; 
            border-radius: 15px; 
            text-align: center;
            border: 1px solid #f1f5f9;
        }
        .stat-label { 
            font-size: 9px; 
            color: #64748b; 
            text-transform: uppercase; 
            letter-spacing: 2px;
            margin-bottom: 8px;
            font-weight: 800;
        }
        .stat-value { 
            font-size: 22px; 
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
            font-size: 12px; 
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
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo-premium.png'))) }}" class="logo-img" alt="Logo" />
        </div>
        <div class="header-right">
            <div class="report-title">Rapport d'Activité Mensuel</div>
            <div class="period-badge">{{ $monthName }} {{ $year }}</div>
        </div>
    </div>

    <table class="stat-grid">
        <tr>
            <td width="33%">
                <div class="stat-card">
                    <div class="stat-label">Chiffre d'Affaires</div>
                    <div class="stat-value">{{ number_format($revenue, 0) }} MAD</div>
                </div>
            </td>
            <td width="33%">
                <div class="stat-card">
                    <div class="stat-label">Total Réservations</div>
                    <div class="stat-value">{{ $reservationsCount }}</div>
                </div>
            </td>
            <td width="33%">
                <div class="stat-card">
                    <div class="stat-label">Taux d'Occupation</div>
                    <div class="stat-value">{{ $occupancyRate }}%</div>
                </div>
            </td>
        </tr>
    </table>

    <h3>Historique des Réservations</h3>
    <table>
        <thead>
            <tr>
                <th>Réf.</th>
                <th>Nom du Client</th>
                <th>Hébergement</th>
                <th>Période</th>
                <th style="text-align: right;">Total Honoré</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservations as $r)
            <tr>
                <td style="font-weight: bold; color: #C9A96E;">{{ $r->reference }}</td>
                <td>{{ $r->user->name }}</td>
                <td>{{ $r->room->room_type?->name }} Chambre {{ $r->room->number }}</td>
                <td>{{ \Carbon\Carbon::parse($r->check_in)->format('d/m') }} au {{ \Carbon\Carbon::parse($r->check_out)->format('d/m') }}</td>
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
