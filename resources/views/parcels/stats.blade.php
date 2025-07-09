@extends('layouts.admin')
     <style>
        .stats-card {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
        }
        
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .stats-number {
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stats-amount {
            font-size: 1.1rem;
            color: #6c757d;
        }
        
        .stats-label {
            font-size: 0.9rem;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .created { color: #17a2b8; }
        .in-transit { color: #ffc107; }
        .delivered { color: #28a745; }
        .returned { color: #dc3545; }
        .pending { color: #6c757d; }
        .exchanged { color: #6f42c1; }
        
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .company-stats {
            margin-top: 30px;
        }
        
        .company-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .performance-metrics {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .metric-item {
            text-align: center;
            padding: 15px;
        }
        
        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .metric-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .chart-container {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .detailed-stats {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-right: 10px;
        }

        .export-buttons {
            margin-bottom: 20px;
        }

        .progress-custom {
            height: 8px;
            border-radius: 4px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Statistiques des Colis
                    </h1>
                    <div class="export-buttons">
                        <!--
                        <a href="#?{{ http_build_query($filters) }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf me-1"></i>
                            Exporter PDF
                        </a>
                        <button class="btn btn-success" onclick="exportToExcel()">
                            <i class="fas fa-file-excel me-1"></i>
                            Exporter Excel
                        </button>
                    -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filter-section">
            <form method="GET" action="{{ route('statistics.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Période</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="date" name="date_from" class="form-control" 
                                       value="{{ $filters['date_from'] ?? '' }}" placeholder="Du">
                            </div>
                            <div class="col-6">
                                <input type="date" name="date_to" class="form-control" 
                                       value="{{ $filters['date_to'] ?? '' }}" placeholder="Au">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Société de livraison</label>
                        <select name="delivery_company_id" class="form-select">
                            <option value="">Toutes les sociétés</option>
                            @foreach($deliveryCompanies as $company)
                                <option value="{{ $company->id }}" 
                                        {{ ($filters['delivery_company_id'] ?? '') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Période graphique</label>
                        <select name="period" class="form-select">
                            <option value="daily" {{ ($filters['period'] ?? 'daily') == 'daily' ? 'selected' : '' }}>Journalier</option>
                            <option value="weekly" {{ ($filters['period'] ?? 'daily') == 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                            <option value="monthly" {{ ($filters['period'] ?? 'daily') == 'monthly' ? 'selected' : '' }}>Mensuel</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="Colis Créé" {{ ($filters['status'] ?? '') == 'Colis Créé' ? 'selected' : '' }}>Colis Créé</option>
                            <option value="Colis créé" {{ ($filters['status'] ?? '') == 'Colis créé' ? 'selected' : '' }}>Colis créé</option>
                            <option value="En transit" {{ ($filters['status'] ?? '') == 'En transit' ? 'selected' : '' }}>En transit</option>
                            <option value="Colis livré" {{ ($filters['status'] ?? '') == 'Colis livré' ? 'selected' : '' }}>Colis livré</option>
                            <option value="Livré - espèce" {{ ($filters['status'] ?? '') == 'Livré - espèce' ? 'selected' : '' }}>Livré - espèce</option>
                            <option value="Retour facturé" {{ ($filters['status'] ?? '') == 'Retour facturé' ? 'selected' : '' }}>Retour facturé</option>
                            <option value="Retourné Dépot" {{ ($filters['status'] ?? '') == 'Retourné Dépot' ? 'selected' : '' }}>Retourné Dépot</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>
                                Filtrer
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Métriques de performance -->
        <div class="performance-metrics">
            <div class="row">
                <div class="col-md-3">
                    <div class="metric-item">
                        <div class="metric-value">{{ number_format($performanceMetrics['delivery_rate'], 1) }}%</div>
                        <div class="metric-label">Taux de livraison</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-item">
                        <div class="metric-value">{{ number_format($performanceMetrics['return_rate'], 1) }}%</div>
                        <div class="metric-label">Taux de retour</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-item">
                        <div class="metric-value">{{ number_format($performanceMetrics['average_delivery_time'], 1) }}</div>
                        <div class="metric-label">Jours moy. livraison</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-item">
                        <div class="metric-value">{{ number_format($performanceMetrics['total_revenue'], 0) }}</div>
                        <div class="metric-label">Chiffre d'affaires (TND)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques principales -->
        <div class="row">
            <div class="col-md-2">
                <div class="stats-card text-center created">
                    <div class="stats-icon created">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="stats-label">Créés</div>
                    <div class="stats-number">{{ $mainStats['created_count'] }}</div>
                    <div class="stats-amount">{{ number_format($mainStats['created_amount'], 0) }} TND</div>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="stats-card text-center in-transit">
                    <div class="stats-icon in-transit">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="stats-label">En transit</div>
                    <div class="stats-number">{{ $mainStats['in_transit_count'] }}</div>
                    <div class="stats-amount">{{ number_format($mainStats['in_transit_amount'], 0) }} TND</div>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="stats-card text-center delivered">
                    <div class="stats-icon delivered">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-label">Livrés</div>
                    <div class="stats-number">{{ $mainStats['delivered_count'] }}</div>
                    <div class="stats-amount">{{ number_format($mainStats['delivered_amount'], 0) }} TND</div>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="stats-card text-center returned">
                    <div class="stats-icon returned">
                        <i class="fas fa-undo"></i>
                    </div>
                    <div class="stats-label">Retournés</div>
                    <div class="stats-number">{{ $mainStats['returned_count'] }}</div>
                    <div class="stats-amount">{{ number_format($mainStats['returned_amount'], 0) }} TND</div>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="stats-card text-center pending">
                    <div class="stats-icon pending">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-label">En attente</div>
                    <div class="stats-number">{{ $mainStats['pending_count'] }}</div>
                    <div class="stats-amount">{{ number_format($mainStats['pending_amount'], 0) }} TND</div>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="stats-card text-center exchanged">
                    <div class="stats-icon exchanged">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="stats-label">Échangés</div>
                    <div class="stats-number">{{ $mainStats['exchanged_count'] }}</div>
                    <div class="stats-amount">{{ number_format($mainStats['exchanged_amount'], 0) }} TND</div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="row">
            <div class="col-md-8">
                <div class="chart-container">
                    <h5 class="mb-3">Évolution des colis par période</h5>
                    <canvas id="periodChart" height="100"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-container">
                    <h5 class="mb-3">Répartition par statut</h5>
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Statistiques par société -->
        <div class="company-stats">
            <h4 class="mb-3">Statistiques par société de livraison</h4>
            <div class="row">
                @foreach($companyStats as $companyId => $stats)
                <div class="col-md-6 mb-4">
                    <div class="company-card">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-building me-2"></i>
                            {{ $stats['company_name'] }}
                        </h5>
                        
                        <div class="row">
                            <div class="col-4 text-center">
                                <div class="text-muted small">Total colis</div>
                                <div class="h5 mb-0">{{ $stats['total_parcels'] }}</div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="text-muted small">Montant total</div>
                                <div class="h5 mb-0">{{ number_format($stats['total_amount'], 0) }} TND</div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="text-muted small">Taux livraison</div>
                                <div class="h5 mb-0 text-success">
                                    {{ $stats['total_parcels'] > 0 ? number_format(($stats['delivered_count'] / $stats['total_parcels']) * 100, 1) : 0 }}%
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-6">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-info">Créés</span>
                                    <span class="badge bg-info">{{ $stats['created_count'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-warning">En transit</span>
                                    <span class="badge bg-warning">{{ $stats['in_transit_count'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-success">Livrés</span>
                                    <span class="badge bg-success">{{ $stats['delivered_count'] }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-danger">Retournés</span>
                                    <span class="badge bg-danger">{{ $stats['returned_count'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-secondary">En attente</span>
                                    <span class="badge bg-secondary">{{ $stats['pending_count'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-purple">Échangés</span>
                                    <span class="badge" style="background-color: #6f42c1;">{{ $stats['exchanged_count'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Barre de progression pour le taux de livraison -->
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Progression des livraisons</small>
                                <small class="text-muted">{{ $stats['total_parcels'] > 0 ? number_format(($stats['delivered_count'] / $stats['total_parcels']) * 100, 1) : 0 }}%</small>
                            </div>
                            <div class="progress progress-custom">
                                <div class="progress-bar bg-success" style="width: {{ $stats['total_parcels'] > 0 ? ($stats['delivered_count'] / $stats['total_parcels']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Statistiques détaillées par statut -->
        <div class="detailed-stats">
            <h4 class="mb-3">Statistiques détaillées par statut</h4>
            @foreach($detailedStats as $companyId => $companyData)
            <div class="mb-4">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-building me-2"></i>
                    {{ $companyData['company_name'] }}
                </h6>
                
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Statut</th>
                                <th>Catégorie</th>
                                <th class="text-end">Nombre</th>
                                <th class="text-end">Montant (TND)</th>
                                <th class="text-end">Pourcentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalParcels = array_sum(array_column($companyData['statuses'], 'count'));
                            @endphp
                            @foreach($companyData['statuses'] as $status => $statusData)
                            <tr>
                                <td>{{ $status }}</td>
                                <td>
                                    @if($statusData['category'] == 'created')
                                        <span class="status-badge bg-info text-white">Créé</span>
                                    @elseif($statusData['category'] == 'in_transit')
                                        <span class="status-badge bg-warning text-dark">En transit</span>
                                    @elseif($statusData['category'] == 'delivered')
                                        <span class="status-badge bg-success text-white">Livré</span>
                                    @elseif($statusData['category'] == 'returned')
                                        <span class="status-badge bg-danger text-white">Retourné</span>
                                    @elseif($statusData['category'] == 'pending')
                                        <span class="status-badge bg-secondary text-white">En attente</span>
                                    @elseif($statusData['category'] == 'exchanged')
                                        <span class="status-badge text-white" style="background-color: #6f42c1;">Échangé</span>
                                    @else
                                        <span class="status-badge bg-light text-dark">Non défini</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ $statusData['count'] }}</td>
                                <td class="text-end">{{ number_format($statusData['amount'], 0) }}</td>
                                <td class="text-end">{{ $totalParcels > 0 ? number_format(($statusData['count'] / $totalParcels) * 100, 1) : 0 }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Résumé total -->
        <div class="row">
            <div class="col-md-12">
                <div class="stats-card">
                    <h5 class="text-center mb-3">Résumé général</h5>
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="h4 text-primary">{{ $mainStats['total_parcels'] }}</div>
                            <div class="text-muted">Total colis</div>
                        </div>
                        <div class="col-md-2">
                            <div class="h4 text-success">{{ number_format($mainStats['total_amount'], 0) }} TND</div>
                            <div class="text-muted">Montant total</div>
                        </div>
                        <div class="col-md-2">
                            <div class="h4 text-info">{{ number_format($performanceMetrics['delivery_rate'], 1) }}%</div>
                            <div class="text-muted">Taux de livraison</div>
                        </div>
                        <div class="col-md-2">
                            <div class="h4 text-warning">{{ number_format($performanceMetrics['return_rate'], 1) }}%</div>
                            <div class="text-muted">Taux de retour</div>
                        </div>
                        <div class="col-md-2">
                            <div class="h4 text-secondary">{{ number_format($performanceMetrics['average_delivery_time'], 1) }}</div>
                            <div class="text-muted">Jours moy. livraison</div>
                        </div>
                        <div class="col-md-2">
                            <div class="h4 text-success">{{ number_format($performanceMetrics['total_revenue'], 0) }} TND</div>
                            <div class="text-muted">Chiffre d'affaires</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Graphique d'évolution par période
        const periodData = @json($periodStats);
        const ctx1 = document.getElementById('periodChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: periodData.map(item => item.date),
                datasets: [{
                    label: 'Créés',
                    data: periodData.map(item => item.created_count),
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    tension: 0.4
                }, {
                    label: 'En transit',
                    data: periodData.map(item => item.in_transit_count),
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Livrés',
                    data: periodData.map(item => item.delivered_count),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Retournés',
                    data: periodData.map(item => item.returned_count),
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Graphique en secteurs pour les statuts
        const statusData = [
            {{ $mainStats['created_count'] }},
            {{ $mainStats['in_transit_count'] }},
            {{ $mainStats['delivered_count'] }},
            {{ $mainStats['returned_count'] }},
            {{ $mainStats['pending_count'] }},
            {{ $mainStats['exchanged_count'] }}
        ];

        const ctx2 = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Créés', 'En transit', 'Livrés', 'Retournés', 'En attente', 'Échangés'],
                datasets: [{
                    data: statusData,
                    backgroundColor: [
                        '#17a2b8',
                        '#ffc107',
                        '#28a745',
                        '#dc3545',
                        '#6c757d',
                        '#6f42c1'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Fonction d'export Excel
        function exportToExcel() {
            // Créer une table HTML avec toutes les données
            let html = '<table>';
            html += '<tr><th>Statut</th><th>Nombre</th><th>Montant</th></tr>';
            
            // Ajouter les données principales
            html += '<tr><td>Créés</td><td>{{ $mainStats["created_count"] }}</td><td>{{ $mainStats["created_amount"] }}</td></tr>';
            html += '<tr><td>En transit</td><td>{{ $mainStats["in_transit_count"] }}</td><td>{{ $mainStats["in_transit_amount"] }}</td></tr>';
            html += '<tr><td>Livrés</td><td>{{ $mainStats["delivered_count"] }}</td><td>{{ $mainStats["delivered_amount"] }}</td></tr>';
            html += '<tr><td>Retournés</td><td>{{ $mainStats["returned_count"] }}</td><td>{{ $mainStats["returned_amount"] }}</td></tr>';
            html += '<tr><td>En attente</td><td>{{ $mainStats["pending_count"] }}</td><td>{{ $mainStats["pending_amount"] }}</td></tr>';
            html += '<tr><td>Échangés</td><td>{{ $mainStats["exchanged_count"] }}</td><td>{{ $mainStats["exchanged_amount"] }}</td></tr>';
            
            html += '</table>';
            
            // Créer un blob et télécharger
            const blob = new Blob([html], {type: 'application/vnd.ms-excel'});
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'statistiques_colis_' + new Date().toISOString().slice(0,10) + '.xls';
            a.click();
            window.URL.revokeObjectURL(url);
        }

        // Actualisation automatique toutes les 5 minutes
        setInterval(function() {
            location.reload();
        }, 300000);
    </script>
@endsection