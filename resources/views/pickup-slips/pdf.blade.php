<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de Ramassage - {{ $pickupSlip->reference }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #007bff;
            font-size: 24px;
            margin: 0;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
        
        .info-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .info-card h3 {
            color: #007bff;
            font-size: 14px;
            margin: 0 0 10px 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 5px 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .info-table td:first-child {
            font-weight: bold;
            width: 40%;
        }
        
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .stats-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .stats-row {
            display: table;
            width: 100%;
        }
        
        .stats-col {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
        }
        
        .stats-value {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        
        .text-primary { color: #007bff; }
        .text-success { color: #28a745; }
        .text-info { color: #17a2b8; }
        .text-warning { color: #ffc107; }
        
        .parcels-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .parcels-table th,
        .parcels-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .parcels-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
        }
        
        .parcels-table td {
            font-size: 10px;
        }
        
        .parcels-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .reference-code {
            font-family: 'Courier New', monospace;
            background-color: #f8f9fa;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 10px;
        }
        
        .barcode {
            text-align: center;
            margin: 5px 0;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>BON DE RAMASSAGE</h1>
        <p>Référence: <span class="reference-code">{{ $pickupSlip->reference }}</span></p>
        <p>Généré le: {{ $generated_at }}</p>
    </div>

    <!-- Informations principales -->
    <div class="info-section">
        <div class="info-column">
            <div class="info-card">
                <h3>Informations générales</h3>
                <table class="info-table">
                    <tr>
                        <td>Date:</td>
                        <td>{{ date('d/m/Y', strtotime($pickupSlip->date)) }}</td>
                    </tr>
                    <tr>
                        <td>Référence:</td>
                        <td><span class="reference-code">{{ $pickupSlip->reference }}</span></td>
                    </tr>
                    <tr>
                        <td>Société de livraison:</td>
                        <td>{{ $deliveryCompany->name }}</td>
                    </tr>
                    <tr>
                        <td>Statut:</td>
                        <td>
                            <span class="badge badge-{{ $pickupSlip->status === 'pending' ? 'warning' : 
                                                       ($pickupSlip->status === 'completed' ? 'success' : 
                                                       ($pickupSlip->status === 'in_progress' ? 'info' : 'danger')) }}">
                                {{ ucfirst($pickupSlip->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Nombre de colis:</td>
                        <td>
                            <span class="badge badge-secondary">{{ $statistics['total_parcels'] }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="info-column">
            <div class="info-card">
                <h3>Informations de création</h3>
                <table class="info-table">
                    <tr>
                        <td>Créé par:</td>
                        <td>{{ $pickupSlip->user->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Date de création:</td>
                        <td>{{ $pickupSlip->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Dernière modification:</td>
                        <td>{{ $pickupSlip->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="stats-section">
        <h3 style="margin-top: 0; color: #007bff;">Statistiques</h3>
        <div class="stats-row">
            <div class="stats-col">
                <div class="stats-value text-primary">{{ $statistics['total_parcels'] }}</div>
                <div class="stats-label">Total colis</div>
            </div>
            <div class="stats-col">
                <div class="stats-value text-success">{{ number_format($statistics['total_cod'], 2) }} Dt</div>
                <div class="stats-label">Total COD</div>
            </div>
            <div class="stats-col">
                <div class="stats-value text-info">{{ $statistics['total_governorates'] }}</div>
                <div class="stats-label">Gouvernorats</div>
            </div>
            <div class="stats-col">
                <div class="stats-value text-warning">{{ $statistics['pending_parcels'] }}</div>
                <div class="stats-label">En attente</div>
            </div>
        </div>
    </div>

    <!-- Liste des colis -->
    <div style="margin-top: 30px;">
        <h3 style="color: #007bff; margin-bottom: 15px;">Liste des colis</h3>
        
        @if($parcels->count() > 0)
            <table class="parcels-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Client</th>
                        <th>Téléphone</th>
                        <th>Gouvernorat</th>
                        <th>Adresse</th>
                        <th>COD</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parcels as $parcel)
                        <tr>
                            <td>
                                <span class="reference-code">{{ $parcel->reference }}</span>
                                @if(isset($barcodes[$parcel->id]))
                                    <div class="barcode">
                                        {!! $barcodes[$parcel->id] !!}
                                    </div>
                                @endif
                            </td>
                            <td>{{ $parcel->nom_client }}</td>
                            <td>{{ $parcel->tel_l }}</td>
                            <td>{{ $parcel->gov_l }}</td>
                            <td>{{ $parcel->adresse_l }}</td>
                            <td>{{ number_format($parcel->cod, 2) }} Dt</td>
                            <td>
                                <span class="badge badge-{{ $parcel->status === 'pending' ? 'warning' : 
                                                           ($parcel->status === 'delivered' ? 'success' : 
                                                           ($parcel->status === 'in_transit' ? 'info' : 'danger')) }}">
                                    {{ ucfirst($parcel->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #666; padding: 20px;">Aucun colis associé à ce bon de ramassage</p>
        @endif
    </div>

    <!-- Signature -->
    <div style="margin-top: 40px; display: table; width: 100%;">
        <div style="display: table-cell; width: 50%; text-align: center;">
            <div style="border: 1px solid #ddd; padding: 40px 20px; margin: 10px;">
                <strong>Signature du responsable</strong>
                <br><br><br>
                <div style="border-bottom: 1px solid #333; width: 80%; margin: 0 auto;"></div>
                <small>Date et signature</small>
            </div>
        </div>
        <div style="display: table-cell; width: 50%; text-align: center;">
            <div style="border: 1px solid #ddd; padding: 40px 20px; margin: 10px;">
                <strong>Signature du livreur</strong>
                <br><br><br>
                <div style="border-bottom: 1px solid #333; width: 80%; margin: 0 auto;"></div>
                <small>Date et signature</small>
            </div>
        </div>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        <p>Document généré automatiquement le {{ $generated_at }}</p>
        <p>Bon de ramassage - {{ $pickupSlip->reference }} - {{ $statistics['total_parcels'] }} colis</p>
    </div>
</body>
</html>