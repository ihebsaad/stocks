<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de Livraison - {{ $parcel->reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header avec logo et titre */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 3px solid #01322e;
            padding-bottom: 20px;
        }
        
        .logo-section {
            flex: 1;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background-color: #01322e;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 10px;
        }
        
        .document-title {
            text-align: right;
            flex: 1;
        }
        
        .document-title h1 {
            color: #01322e;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .document-subtitle {
            color: #666;
            font-size: 14px;
        }
        
        /* Section de référence et code-barres */
        .reference-section {
            background-color: #f8f9fa;
            border: 2px solid #01322e;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .reference-info {
            flex: 1;
        }
        
        .reference-label {
            color: #01322e;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .reference-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .date-system {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        
        .barcode-section {
            text-align: center;
            flex: 1;
        }
        
        .barcode {
            font-family: 'Courier New', monospace;
            font-size: 20px;
            letter-spacing: 2px;
            color: #01322e;
            margin-bottom: 5px;
        }
        
        .barcode-lines {
            height: 40px;
            background: repeating-linear-gradient(
                90deg,
                #000 0px,
                #000 2px,
                #fff 2px,
                #fff 4px
            );
            margin-bottom: 5px;
        }
        
        /* Sections principales */
        .main-sections {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }
        
        .section {
            flex: 1;
            border: 2px solid #01322e;
            padding: 15px;
            background-color: #fff;
        }
        
        .section-title {
            background-color: #01322e;
            color: white;
            padding: 8px 12px;
            margin: -15px -15px 15px -15px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .info-row {
            margin-bottom: 8px;
            display: flex;
            align-items: flex-start;
        }
        
        .info-label {
            font-weight: bold;
            color: #01322e;
            min-width: 80px;
            margin-right: 10px;
        }
        
        .info-value {
            flex: 1;
            color: #333;
        }
        
        /* Badge transporteur */
        .company-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 11px;
            color: white;
            text-transform: uppercase;
        }
        
        .bg-1 { background-color: #0da598; }
        .bg-2 { background-color: #ef6f28; }
        .bg-3 { background-color: #227ac2; }
        .bg-4 { background-color: #6c757d; }
        .bg-5 { background-color: #fd9883; }
        
        /* Remarque */
        .remarque-section {
            margin-bottom: 20px;
        }
        
        .remarque-title {
            color: #01322e;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .remarque-content {
            border: 2px solid #dc3545;
            background-color: #fff5f5;
            padding: 10px;
            color: #dc3545;
            font-weight: bold;
            min-height: 40px;
        }
        
        /* Tableau des articles */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 2px solid #01322e;
        }
        
        .items-table thead {
            background-color: #01322e;
            color: white;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #01322e;
            padding: 8px;
            text-align: center;
        }
        
        .items-table th {
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .items-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .total-row {
            background-color: #e8f5e8;
            font-weight: bold;
        }
        
        /* Montant COD */
        .cod-amount {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
        }
        
        .currency {
            font-size: 10px;
            vertical-align: super;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            border-top: 2px solid #01322e;
            padding-top: 15px;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        
        /* Responsive pour PDF */
        @media print {
            .container {
                padding: 10px;
            }
            
            .main-sections {
                flex-direction: column;
            }
            
            .section {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <div class="logo">LOGO</div>
            </div>
            <div class="document-title">
                <h1>Bon de livraison BL</h1>
                <div class="document-subtitle">id colis</div>
            </div>
        </div>
        
        <!-- Référence et Code-barres -->
        <div class="reference-section">
            <div class="reference-info">
                <div class="reference-label">Référence :</div>
                <div class="reference-value">{{ $parcel->reference }}</div>
                <div class="date-system">
                    date syst: {{ $parcel->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
            <div class="barcode-section">
                <div style="font-weight: bold; margin-bottom: 5px;">code à barre</div>
                <div style="font-weight: bold; margin-bottom: 5px;">généré</div>
                <div class="barcode-lines"></div>
            </div>
        </div>
        
        <!-- Sections principales -->
        <div class="main-sections">
            <!-- Expéditeur -->
            <div class="section">
                <div class="section-title">Expéditeur</div>
                <div class="info-row">
                    <span class="info-label">Nom:</span>
                    <span class="info-value">{{ $expediteur['nom'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Adresse:</span>
                    <span class="info-value">{{ $expediteur['adresse'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tél:</span>
                    <span class="info-value">{{ $expediteur['telephone'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">MF:</span>
                    <span class="info-value">{{ $expediteur['mf'] }}</span>
                </div>
            </div>
            
            <!-- Transporteur -->
            <div class="section">
                <div class="section-title">Transporteur</div>
                <div style="text-align: center; padding: 20px;">
                    <span class="company-badge bg-{{ $parcel->company->id }}">
                        {{ strtoupper($parcel->company->name) }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Client -->
        <div class="section" style="margin-bottom: 20px;">
            <div class="section-title">Client</div>
            <div class="info-row">
                <span class="info-label">Nom:</span>
                <span class="info-value">{{ $parcel->nom_client }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tél:</span>
                <span class="info-value">{{ $parcel->tel_l }}</span>
            </div>
            @if($parcel->tel2_l)
            <div class="info-row">
                <span class="info-label">Tél 2:</span>
                <span class="info-value">{{ $parcel->tel2_l }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Ville:</span>
                <span class="info-value">{{ $parcel->ville_cl }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Délégation:</span>
                <span class="info-value">{{ $parcel->gov_l }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Adresse:</span>
                <span class="info-value">{{ $parcel->adresse_l }}</span>
            </div>
        </div>
        
        <!-- Remarque -->
        @if($parcel->remarque)
        <div class="remarque-section">
            <div class="remarque-title">Remarque</div>
            <div class="remarque-content">
                {{ $parcel->remarque }}
            </div>
        </div>
        @else
        <div class="remarque-section">
            <div class="remarque-title">Remarque</div>
            <div class="remarque-content" style="border-color: #ddd; background-color: #f8f9fa; color: #666;">
                <!-- Espace pour remarques -->
            </div>
        </div>
        @endif
        
        <!-- Tableau des articles -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>DÉSIGNATION</th>
                    <th>PU</th>
                    <th>QTÉ</th>
                    <th>TOT</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($parcel->order->items as $item)
                @php 
                    $itemTotal = $item->unit_price * $item->quantity;
                    $total += $itemTotal;
                @endphp
                <tr>
                    <td style="text-align: left;">{{ $item->product->name }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($itemTotal, 2) }} <span class="currency">TND</span></td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL:</td>
                    <td style="font-weight: bold;">
                        <span class="cod-amount">{{ number_format($total, 2) }} <span class="currency">TND</span></span>
                    </td>
                </tr>
                @if($parcel->cod > 0)
                <tr style="background-color: #fff3cd; color: #856404;">
                    <td colspan="3" style="text-align: right; font-weight: bold;">COD À COLLECTER:</td>
                    <td style="font-weight: bold;">
                        <span class="cod-amount" style="color: #856404;">{{ number_format($parcel->cod, 2) }} <span class="currency">TND</span></span>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
        
        <!-- Footer -->
        <div class="footer">
            <p>Bon de livraison généré le {{ now()->format('d/m/Y à H:i') }}</p>
            <p>{{ $expediteur['nom'] }} - {{ $expediteur['adresse'] }} - Tél: {{ $expediteur['telephone'] }}</p>
        </div>
    </div>
</body>
</html>