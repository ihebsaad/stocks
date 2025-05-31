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
        
        /* Header compact */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            border-bottom: 2px solid #01322e;
            padding-bottom: 5px;
        }
        
        .logo {
            width: 35px;
            height: 35px;
            background-color: #01322e;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            border-radius: 3px;
        }
        
        .document-title {
            text-align: center;
            flex: 1;
            margin: 0 10px;
        }
        
        .document-title h1 {
            color: #01322e;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .document-subtitle {
            color: #666;
            font-size: 8px;
        }
        
        /* Section référence et code-barres */
        .reference-barcode {
            display: flex;
            border: 1px solid #01322e;
            margin-bottom: 8px;
            background: #f8f9fa;
        }
        
        .reference-left {
            flex: 1;
            padding: 8px;
            border-right: 1px solid #01322e;
        }
        
        .ref-label {
            color: #01322e;
            font-weight: bold;
            font-size: 9px;
        }
        
        .ref-value {
            font-size: 12px;
            font-weight: bold;
            margin: 2px 0;
        }
        
        .date-sys {
            font-size: 8px;
            color: #666;
        }
        
        .barcode-right {
            width: 80px;
            padding: 5px 10px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .barcode-title {
            font-size: 7px;
            color: #01322e;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .barcode-svg {
            width: 100%;
            height: 30px;
            margin-bottom: 2px;
        }
        
        .barcode-text {
            /*font-size: 6px;
            color: #333;*/
            font-family: monospace;
            font-size:15px;
            font-weight: bold;
        }
        
        /* Sections en ligne */
        .info-sections {
            gap: 5px;
            margin-bottom: 8px;
        }
        
        .info-section {
            border: 1px solid #01322e;
            background: white;
        }
        .client-section .info-section {
            border: 1px solid #3f5367!important;
            background: white;
        }
        
        .section-header {
            background: #01322e;
            color: white;
            padding: 3px 6px;
            font-weight: bold;
            font-size: 9px;
            text-align: center;
        }
        
        .section-content {
            padding: 6px;
        }
        
        .info-line {
            display: flex;
            margin-bottom: 3px;
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
        .client-section .info-label {
            color:#3f5367!important;
        }
        .info-value {
            flex: 1;
            word-break: break-word;
        }
        
        /* Transporteur centré */
        .transporteur-section {
            text-align: center;
            /*padding: 15px 6px;*/
        }
        
        .company-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 10px;
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
            border: 1px solid #3f5367;
            margin-bottom: 8px;
            background: white;
        }
        
        .client-content {
            padding: 6px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .client-col {
            flex: 1;
            min-width: 45%;
        }
        
        /* Remarque si existe */
        .remarque-section {
            border: 2px solid #dc3545;
            background: #fff5f5;
            margin-bottom: 8px;
            padding: 6px;
        }
        
        .remarque-title {
            color: #dc3545;
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 3px;
        }
        
        .remarque-text {
            color: #dc3545;
            font-size: 8px;
            font-weight: bold;
        }
        
        /* Tableau compact */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 8px;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #01322e;
            padding: 3px;
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
        
        /* Footer minimal */
        .footer {
            text-align: center;
            font-size: 7px;
            color: #666;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid #ddd;
        }
        table{
            width:100%;
        }
        .text-center{
            text-align:center;
        }
        .text-right{
            text-align:right;
        }
        .transporteur,.expediteur{
            min-height: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <table class="text-center">
            <tr>
                <td>
                    <img src="{{ asset('img/logo.png')}}" style="width:70px" /><br>
                    <b>zahome.tn<b>
                </td>
                <td>
                    <div class="document-title">
                        <h1>BL {{ $parcel->id}}</h1>
                        <div class="date-sys">Date : {{ $parcel->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </td>
                <td><!--
                <div class="reference-barcode">
                    <div class="reference-left">
                        <div class="ref-value">{{ $parcel->reference }}</div>
                    </div>
                </div>-->
                <div class="barcode-section" style="padding-right:40px">
                    <div class="barcode-container barcode-right">
                        <!-- Code-barres simple avec CSS -->
                        <div class="simple-barcode">
                            <div class="barcode-lines">
                                @php
                                    // Génération simple de barres basée sur la référence
                                    echo $barcode;
                                @endphp
 
                            </div>
                        </div>
                    </div>
                    <div class="barcode-text" style="padding-left:15px">{{ $parcel->reference }}</div>
                </div>
                </td>
            </tr>
        </table>

        <!-- Expéditeur + Transporteur -->
        <table  >
            <tr>
                <td style="width:40%">
                    <div class="section-header">Expéditeur</div>
                        <div class="section-content expediteur">
                            <table>
                                <tr>
                                    <td>
                                        <div class="info-line">
                                            <span class="info-label">Nom:</span>
                                            <span class="info-value">Z&A Home</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="info-line">
                                            <span class="info-label">Adresse:</span>
                                            <span class="info-value">Ksibet médiouni</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="info-line">
                                            <span class="info-label">Tél:</span>
                                            <span class="info-value">55 969 997</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="info-line">
                                            <span class="info-label">MF:</span>
                                            <span class="info-value">1768373/Z/P/M/000</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
                <td style="width:60%" >
                    <div class="section-header">Transporteur</div>
                        <div class="section-content transporteur">
                            <table>
                                <tr >
                                    <td>
                                        <div class="transporteur-section">
                                            <span class="company-badge bg-{{ $parcel->company->id }}">
                                                {{ strtoupper($parcel->company->name) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="info-line">
                                            <span class="info-label">Tél:</span>
                                            <span class="info-value">{{$parcel->company->phone}}</span>
                                        </div>
                                        <div class="info-line">
                                            <span class="info-label">Adresse:</span>
                                            <span class="info-value">{{$parcel->company->address}}</span>
                                        </div>                                      
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        
        <!-- Client -->

        <div class="client-section">
        <div class="section-header" style="background-color: #3f5367">Client</div>
            <table>
                <tr>
                    <td>
                        <div class="info-line">
                            <span class="info-label">Nom:</span>
                            <span class="info-value">{{ $parcel->nom_client }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="info-line">
                            <span class="info-label">Tél:</span>
                            <span class="info-value">{{ $parcel->tel_l }}</span>
                        </div>
                    </td>
                    <td>
                        @if($parcel->tel2_l!='')
                        <div class="info-line">
                            <span class="info-label">Tél2:</span>
                            <span class="info-value">{{ $parcel->tel2_l }}</span>
                        </div>
                        @endif
                    </td>                    
                    <td>
                        <div class="info-line">
                            <span class="info-label">Ville:</span>
                            <span class="info-value">{{ $parcel->ville_cl }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="info-line">
                            <span class="info-label">Délégation:</span>
                            <span class="info-value">{{ $parcel->gov_l }}</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="info-line">
                            <span class="info-label">Adresse:</span>
                            <span class="info-value">{{ $parcel->adresse_l }}</span>
                        </div>
                    </td>
                    <td colspan="3">
                        @if($parcel->remarque!='')
                        <div class="info-line">
                            <span class="info-label">Remarque:</span>
                            <span class="info-value" style="color: #d03132;font-weight:bold">{{$parcel->remarque}}</span>
                        </div>
                        @endif
                    </td>
                </tr>
            </table>
 
        </div>
        
        
        <!-- Tableau des articles -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="designation">DESIGNATION</th>
                    <th>PU</th>
                    <th>QTE</th>
                    <th>TOTAL</th>
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
                    <td colspan="3" style="text-align: right;">COD:</td>
                    <td class="amount">{{ number_format($parcel->cod, 2) }} <span class="currency">TND</span></td>
                </tr>
            </tbody>
        </table>
        
        <!-- Footer minimal -->
        <div class="footer">
            BL généré le {{ now()->format('d/m/Y H:i') }} - Z&A Home
        </div>
    </div>
</body>
</html>