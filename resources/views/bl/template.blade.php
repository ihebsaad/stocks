<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BL - {{ $parcel->reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #333;
            width: 148mm; /* A5 width */
            margin: 0 auto;
        }
        
        .container {
            padding: 8px;
            max-width: 100%;
        }
        
        /* Header avec logo et titre */
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #01322e;
            padding-bottom: 8px;
        }
        
        .logo {
            width: 40px;
            height: 40px;
            background-color: #01322e;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            border-radius: 5px;
            margin-right: 15px;
        }
        
        .document-title h1 {
            color: #01322e;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .document-subtitle {
            color: #666;
            font-size: 9px;
        }
        
        /* Section référence + code-barres sur la même ligne */
        .reference-barcode-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .reference-section {
            flex: 1;
            border: 1px solid #01322e;
            padding: 8px;
            background: #f8f9fa;
        }
        
        .reference-section .section-title {
            color: #01322e;
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 5px;
        }
        
        .ref-value {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .date-sys {
            font-size: 8px;
            color: #666;
        }
        
        .barcode-section {
            width: 100px;
            border: 1px solid #01322e;
            padding: 8px;
            text-align: center;
            background: white;
        }
        
        .barcode-section .section-title {
            color: #01322e;
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 5px;
        }
        
        .barcode-container {
            margin-bottom: 5px;
        }
        
        .barcode-text {
            font-size: 7px;
            color: #333;
            font-family: monospace;
        }
        
        /* Expéditeur et Transporteur sur la même ligne */
        .expediteur-transporteur-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .expediteur-section,
        .transporteur-section {
            flex: 1;
            border: 1px solid #01322e;
            background: white;
        }
        
        .section-header {
            background: #01322e;
            color: white;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 9px;
            text-align: center;
        }
        
        .section-content {
            padding: 8px;
        }
        
        .info-line {
            display: flex;
            margin-bottom: 4px;
            font-size: 8px;
        }
        
        .info-line:last-child {
            margin-bottom: 0;
        }
        
        .info-label {
            font-weight: bold;
            color: #01322e;
            min-width: 35px;
            margin-right: 5px;
        }
        
        .info-value {
            flex: 1;
            word-break: break-word;
        }
        
        /* Transporteur avec badge centré */
        .transporteur-content {
            padding: 15px 8px;
            text-align: center;
        }
        
        .company-badge {
            display: inline-block;
            padding: 8px 15px;
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
        
        /* Section client pleine largeur */
        .client-section {
            border: 1px solid #01322e;
            margin-bottom: 10px;
            background: white;
        }
        
        .client-content {
            padding: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .client-col {
            flex: 1;
            min-width: 45%;
        }
        
        /* Remarque conditionnelle */
        .remarque-section {
            border: 2px solid #dc3545;
            background: #fff5f5;
            margin-bottom: 10px;
            padding: 8px;
        }
        
        .remarque-title {
            color: #dc3545;
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 5px;
        }
        
        .remarque-text {
            color: #dc3545;
            font-size: 8px;
            font-weight: bold;
        }
        
        /* Tableau des articles */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8px;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #01322e;
            padding: 4px;
            text-align: center;
        }
        
        .items-table th {
            background: #01322e;
            color: white;
            font-weight: bold;
            font-size: 8px;
        }
        
        .items-table .designation {
            text-align: left;
            max-width: 60px;
            word-break: break-word;
        }
        
        .items-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .total-row {
            background: #e8f5e8 !important;
            font-weight: bold;
        }
        
        .cod-row {
            background: #fff3cd !important;
            color: #856404;
            font-weight: bold;
        }
        
        .amount {
            font-weight: bold;
        }
        
        .currency {
            font-size: 6px;
            vertical-align: super;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            font-size: 7px;
            color: #666;
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px solid #ddd;
        }
        
        /* Styles pour le code-barres simple */
        .simple-barcode {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 35px;
            background: #f8f9fa;
            border: 1px dashed #01322e;
            margin-bottom: 5px;
        }
        
        .barcode-lines {
            display: flex;
            align-items: center;
            height: 25px;
        }
        
        .bar {
            background: #000;
            margin: 0 1px;
        }
        
        .bar-thick {
            width: 3px;
            height: 25px;
        }
        
        .bar-thin {
            width: 1px;
            height: 25px;
        }
        
        .bar-medium {
            width: 2px;
            height: 25px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">LOGO</div>
            <div class="document-title">
                <h1>Bon de livraison BL</h1>
                <div class="document-subtitle">id colis</div>
            </div>
        </div>
        
        <!-- Référence + Code-barres sur la même ligne -->
        <div class="reference-barcode-row">
            <div class="reference-section">
                <div class="section-title">Référence +</div>
                <div class="ref-value">{{ $parcel->reference }}</div>
                <div class="date-sys">date syst: {{ $parcel->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="barcode-section">
                <div class="section-title">code à barre généré</div>
                <div class="barcode-container">
                    <!-- Code-barres simple avec CSS -->
                    <div class="simple-barcode">
                        <div class="barcode-lines">
                            @php
                                // Génération simple de barres basée sur la référence
                                $reference = $parcel->reference;
                                $seed = crc32($reference);
                                srand($seed);
                            @endphp
                            @for($i = 0; $i < 20; $i++)
                                @php
                                    $random = rand(1, 3);
                                    $class = $random == 1 ? 'bar-thin' : ($random == 2 ? 'bar-medium' : 'bar-thick');
                                @endphp
                                <div class="bar {{ $class }}"></div>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="barcode-text">{{ $parcel->reference }}</div>
            </div>
        </div>
        
        <!-- Expéditeur et Transporteur sur la même ligne -->
        <div class="expediteur-transporteur-row">
            <div class="expediteur-section">
                <div class="section-header">Expéditeur</div>
                <div class="section-content">
                    <div class="info-line">
                        <span class="info-label">Nom:</span>
                        <span class="info-value">Z&A Home</span>
                    </div>
                    <div class="info-line">
                        <span class="info-label">Adr:</span>
                        <span class="info-value">Ksibet médiouni</span>
                    </div>
                    <div class="info-line">
                        <span class="info-label">Tél:</span>
                        <span class="info-value">55 969 997</span>
                    </div>
                    <div class="info-line">
                        <span class="info-label">MF:</span>
                        <span class="info-value">1768373/Z/P/M/000</span>
                    </div>
                </div>
            </div>
            
            <div class="transporteur-section">
                <div class="section-header">Transporteur</div>
                <div class="transporteur-content">
                    <span class="company-badge bg-{{ $parcel->company->id }}">
                        {{ strtoupper($parcel->company->name) }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Section Client -->
        <div class="client-section">
            <div class="section-header">Client</div>
            <div class="client-content">
                <div class="client-col">
                    <div class="info-line">
                        <span class="info-label">Nom:</span>
                        <span class="info-value">{{ $parcel->nom_client }}</span>
                    </div>
                    <div class="info-line">
                        <span class="info-label">Tél:</span>
                        <span class="info-value">{{ $parcel->tel_l }}</span>
                    </div>
                    @if($parcel->tel2_l)
                    <div class="info-line">
                        <span class="info-label">Tél2:</span>
                        <span class="info-value">{{ $parcel->tel2_l }}</span>
                    </div>
                    @endif
                </div>
                <div class="client-col">
                    <div class="info-line">
                        <span class="info-label">Ville:</span>
                        <span class="info-value">{{ $parcel->ville_cl }}</span>
                    </div>
                    <div class="info-line">
                        <span class="info-label">Dél:</span>
                        <span class="info-value">{{ $parcel->gov_l }}</span>
                    </div>
                    <div class="info-line">
                        <span class="info-label">Adr:</span>
                        <span class="info-value">{{ $parcel->adresse_l }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Remarque (conditionnelle) -->
        @if($parcel->remarque)
        <div class="remarque-section">
            <div class="remarque-title">Remarque</div>
            <div class="remarque-text">{{ $parcel->remarque }}</div>
        </div>
        @endif
        
        <!-- Tableau des articles -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="designation">DESIGNATION</th>
                    <th>PU</th>
                    <th>QTE</th>
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
                    <td class="designation">{{ $item->product->name }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="amount">{{ number_format($itemTotal, 2) }} <span class="currency">TND</span></td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">TOTAL:</td>
                    <td class="amount">{{ number_format($total, 2) }} <span class="currency">TND</span></td>
                </tr>
                @if($parcel->cod > 0)
                <tr class="cod-row">
                    <td colspan="3" style="text-align: right;">COD:</td>
                    <td class="amount">{{ number_format($parcel->cod, 2) }} <span class="currency">TND</span></td>
                </tr>
                @endif
            </tbody>
        </table>
        
        <!-- Footer -->
        <div class="footer">
            BL généré le {{ now()->format('d/m/Y H:i') }} - Z&A Home
        </div>
    </div>
</body>
</html>