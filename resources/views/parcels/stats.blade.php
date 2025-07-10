
@extends('layouts.admin')
@section('style')
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            pointer-events: none;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 45px rgba(0,0,0,0.2);
        }
        
        .stats-card.created {
            background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
        }
        
        .stats-card.in-transit {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        }
        
        .stats-card.delivered {
            background: linear-gradient(135deg, #10B981 0%, #047857 100%);
        }
        
        .stats-card.returned {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
        }
        
        .stats-card.pending {
            background: linear-gradient(135deg, #6B7280 0%, #374151 100%);
        }
        
        .stats-card.other {
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
        }
        
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: rgba(255,255,255,0.9);
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .stats-amount {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.8);
            font-weight: 500;
        }
        
        .stats-label {
            font-size: 0.95rem;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 12px;
            color: rgba(255,255,255,0.9);
            letter-spacing: 0.5px;
        }
        
        .filter-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .company-stats {
            margin-top: 30px;
        }
        
        .company-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .company-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }
        
        .performance-metrics {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        
        .metric-item {
            text-align: center;
            padding: 20px;
        }
        
        .metric-value {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .metric-label {
            font-size: 0.9rem;
            opacity: 0.85;
            font-weight: 500;
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .detailed-stats {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-right: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .export-buttons {
            margin-bottom: 20px;
        }

        .progress-custom {
            height: 10px;
            border-radius: 5px;
            margin-top: 8px;
            overflow: hidden;
            background: rgba(0,0,0,0.1);
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .main-stats-container {
            margin-bottom: 30px;
        }

        .status-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .single-status-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid #667eea;
        }

        .single-status-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .status-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .status-card-icon {
            font-size: 1.5rem;
            margin-right: 12px;
            padding: 10px;
            border-radius: 8px;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .status-card-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #374151;
            margin: 0;
        }

        .status-card-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-card-count {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f2937;
        }

        .status-card-amount {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 500;
        }

        .status-card-percentage {
            font-size: 0.8rem;
            color: #667eea;
            font-weight: 600;
        }

        .no-data-message {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
            font-size: 1.1rem;
        }

        .no-data-message i {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #d1d5db;
        }
    </style>
@endsection

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

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
        <form method="GET" action="{{ route('stats') }}">
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
    @if(isset($filters['delivery_company_id']) && $filters['delivery_company_id'] != '')
        <!-- Affichage pour une société spécifique -->
        @if(!empty($companyStats['status_stats']))
            <div class="main-stats-container">
                <h4 class="mb-3">
                    <i class="fas fa-building me-2"></i>
                    {{ $companyStats['company_name'] }}
                </h4>
                <div class="status-stats-grid">
                    @foreach($companyStats['status_stats'] as $status)
                        <div class="single-status-card">
                            <div class="status-card-header">
                                <div class="status-card-icon">
                                    <i class="{{ $status['icon'] }}"></i>
                                </div>
                                <h6 class="status-card-title">{{ $status['label'] }}</h6>
                            </div>
                            <div class="status-card-stats">
                                <div>
                                    <div class="status-card-count">{{ $status['count'] }}</div>
                                    <div class="status-card-amount">{{ number_format($status['amount'], 0) }} TND</div>
                                </div>
                                <div class="status-card-percentage">{{ $status['percentage'] }}%</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="no-data-message">
                <i class="fas fa-inbox"></i>
                <p>Aucune donnée disponible pour cette société dans la période sélectionnée.</p>
            </div>
        @endif
    @else
        <!-- Affichage général pour toutes les sociétés -->
        <div class="main-stats-container">
            <div class="row">
                <div class="col-md-2">
                    <div class="stats-card text-center created">
                        <div class="stats-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="stats-label">Créés</div>
                        <div class="stats-number">{{ $mainStats['created_count'] }}</div>
                        <div class="stats-amount">{{ number_format($mainStats['created_amount'], 0) }} TND</div>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="stats-card text-center in-transit">
                        <div class="stats-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="stats-label">En transit</div>
                        <div class="stats-number">{{ $mainStats['in_transit_count'] }}</div>
                        <div class="stats-amount">{{ number_format($mainStats['in_transit_amount'], 0) }} TND</div>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="stats-card text-center delivered">
                        <div class="stats-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stats-label">Livrés</div>
                        <div class="stats-number">{{ $mainStats['delivered_count'] }}</div>
                        <div class="stats-amount">{{ number_format($mainStats['delivered_amount'], 0) }} TND</div>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="stats-card text-center returned">
                        <div class="stats-icon">
                            <i class="fas fa-undo"></i>
                        </div>
                        <div class="stats-label">Retournés</div>
                        <div class="stats-number">{{ $mainStats['returned_count'] }}</div>
                        <div class="stats-amount">{{ number_format($mainStats['returned_amount'], 0) }} TND</div>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="stats-card text-center pending">
                        <div class="stats-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stats-label">En attente</div>
                        <div class="stats-number">{{ $mainStats['pending_count'] }}</div>
                        <div class="stats-amount">{{ number_format($mainStats['pending_amount'], 0) }} TND</div>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="stats-card text-center other">
                        <div class="stats-icon">
                            <i class="fas fa-ellipsis-h"></i>
                        </div>
                        <div class="stats-label">Autres</div>
                        <div class="stats-number">{{ $mainStats['other_count'] }}</div>
                        <div class="stats-amount">{{ number_format($mainStats['other_amount'], 0) }} TND</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

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

    @if(!isset($filters['delivery_company_id']) || $filters['delivery_company_id'] == '')
        <!-- Statistiques par société (seulement si toutes les sociétés sont sélectionnées) -->
        <div class="company-stats">
            <h4 class="mb-3">Statistiques par société de livraison</h4>
            <div class="row">
                @foreach($companyStats as $stats)
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
                                    @php
                                        $deliveredCount = collect($stats['status_stats'])->where('key', 'delivered')->sum('count') + 
                                                         collect($stats['status_stats'])->where('key', 'delivered_cash')->sum('count') + 
                                                         collect($stats['status_stats'])->where('key', 'paid')->sum('count');
                                    @endphp
                                    {{ $stats['total_parcels'] > 0 ? number_format(($deliveredCount / $stats['total_parcels']) * 100, 1) : 0 }}%
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Affichage des statuts détaillés -->
                        <div class="row">
                            @foreach($stats['status_stats'] as $status)
                                <div class="col-12 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="{{ $status['icon'] }} me-2" style="color: {{ $status['color'] }}"></i>
                                            <span style="font-size: 0.9rem;">{{ $status['label'] }}</span>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge" style="background-color: {{ $status['color'] }}">{{ $status['count'] }}</span>
                                            <small class="text-muted ms-2">{{ $status['percentage'] }}%</small>
                                        </div>
                                    </div>
                                    <div class="progress progress-custom mt-1">
                                        <div class="progress-bar" style="width: {{ $status['percentage'] }}%; background-color: {{ $status['color'] }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Résumé total -->
    <div class="row">
        <div class="col-md-12">
            <div class="stats-card" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); color: #1f2937;">
                <h5 class="text-center mb-3" style="color: #1f2937;">Résumé général</h5>
                <div class="row text-center">
                    <div class="col-md-2">
                        <div class="h4 text-primary">{{ $mainStats['total_parcels'] ?? 0 }}</div>
                        <div class="text-muted">Total colis</div>
                    </div>
                    <div class="col-md-2">
                        <div class="h4 text-success">{{ number_format($mainStats['total_amount'] ?? 0, 0) }} TND</div>
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
        labels: periodData.map(item => item.formatted_date),
        datasets: [{
            label: 'Créés',
            data: periodData.map(item => item.created_count),
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'En transit',
            data: periodData.map(item => item.in_transit_count),
            borderColor: '#F59E0B',
            backgroundColor: 'rgba(245, 158, 11, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Livrés',
            data: periodData.map(item => item.delivered_count),
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Retournés',
            data: periodData.map(item => item.returned_count),
            borderColor: '#EF4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 20,
                    font: {
                        size: 12
                    }
                }
            },
            title: {
                display: true,
                text: 'Évolution des colis par période',
                font: {
                    size: 16,
                    weight: 'bold'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                },
                ticks: {
                    font: {
                        size: 11
                    }
                }
            },
            x: {
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                },
                ticks: {
                    font: {
                        size: 11
                    }
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        },
        hover: {
            mode: 'nearest',
            intersect: true
        }
    }
});

// Graphique en secteurs pour les statuts
const statusData = @json($statusStats);
const ctx2 = document.getElementById('statusChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: statusData.map(item => item.label),
        datasets: [{
            data: statusData.map(item => item.count),
            backgroundColor: [
                '#3B82F6',
                '#F59E0B',
                '#10B981',
                '#EF4444',
                '#6B7280',
                '#8B5CF6'
            ],
            borderColor: [
                '#1E40AF',
                '#D97706',
                '#047857',
                '#DC2626',
                '#374151',
                '#7C3AED'
            ],
            borderWidth: 2,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            },
            title: {
                display: true,
                text: 'Répartition par statut',
                font: {
                    size: 16,
                    weight: 'bold'
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} colis (${percentage}%)`;
                    }
                }
            }
        },
        cutout: '60%'
    }
});

// Fonction d'export Excel
function exportToExcel() {
    const currentUrl = new URL(window.location.href);
    const params = new URLSearchParams(currentUrl.search);
    params.set('export', 'excel');
    
    const exportUrl = `${currentUrl.pathname}?${params.toString()}`;
    window.open(exportUrl, '_blank');
}

// Fonction d'export PDF
function exportToPdf() {
    const currentUrl = new URL(window.location.href);
    const params = new URLSearchParams(currentUrl.search);
    params.set('export', 'pdf');
    
    const exportUrl = `${currentUrl.pathname}?${params.toString()}`;
    window.open(exportUrl, '_blank');
}

// Fonction pour actualiser les graphiques
function refreshCharts() {
    // Récupérer les nouvelles données via AJAX
    const formData = new FormData(document.querySelector('form'));
    const params = new URLSearchParams(formData);
    
    fetch(`/statistics/chart-data?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            // Mettre à jour les graphiques avec les nouvelles données
            updatePeriodChart(data.period);
            updateStatusChart(data.status);
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des données:', error);
        });
}

// Fonction pour mettre à jour le graphique de période
function updatePeriodChart(data) {
    const chart = Chart.getChart('periodChart');
    if (chart) {
        chart.data.labels = data.map(item => item.formatted_date);
        chart.data.datasets[0].data = data.map(item => item.created_count);
        chart.data.datasets[1].data = data.map(item => item.in_transit_count);
        chart.data.datasets[2].data = data.map(item => item.delivered_count);
        chart.data.datasets[3].data = data.map(item => item.returned_count);
        chart.update();
    }
}

// Fonction pour mettre à jour le graphique de statut
function updateStatusChart(data) {
    const chart = Chart.getChart('statusChart');
    if (chart) {
        chart.data.labels = data.map(item => item.label);
        chart.data.datasets[0].data = data.map(item => item.count);
        chart.update();
    }
}

// Fonction pour formater les nombres
function formatNumber(num) {
    return new Intl.NumberFormat('fr-FR').format(num);
}

// Fonction pour animer les compteurs
function animateCounters() {
    const counters = document.querySelectorAll('.stats-number');
    
    counters.forEach(counter => {
        const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
        const duration = 1000; // 1 seconde
        const step = target / (duration / 16); // 60 FPS
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                counter.textContent = formatNumber(target);
                clearInterval(timer);
            } else {
                counter.textContent = formatNumber(Math.floor(current));
            }
        }, 16);
    });
}

// Fonction pour imprimer les statistiques
function printStatistics() {
    window.print();
}

// Fonction pour copier le lien des statistiques
function copyStatisticsLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        // Afficher une notification de succès
        showNotification('Lien copié dans le presse-papiers', 'success');
    }).catch(err => {
        console.error('Erreur lors de la copie:', err);
    });
}

// Fonction pour afficher des notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Supprimer automatiquement après 3 secondes
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Fonction pour valider les dates
function validateDates() {
    const dateFrom = document.querySelector('input[name="date_from"]').value;
    const dateTo = document.querySelector('input[name="date_to"]').value;
    
    if (dateFrom && dateTo) {
        const fromDate = new Date(dateFrom);
        const toDate = new Date(dateTo);
        
        if (fromDate > toDate) {
            showNotification('La date de début doit être antérieure à la date de fin', 'warning');
            return false;
        }
        
        // Vérifier que la période n'est pas trop large (plus de 1 an)
        const diffTime = Math.abs(toDate - fromDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays > 365) {
            showNotification('La période ne peut pas dépasser 1 an', 'warning');
            return false;
        }
    }
    
    return true;
}

// Fonction pour réinitialiser les filtres
function resetFilters() {
    document.querySelector('form').reset();
    window.location.href = window.location.pathname;
}

// Gestionnaire d'événements pour le formulaire
document.addEventListener('DOMContentLoaded', function() {
    // Animer les compteurs au chargement
    animateCounters();
    
    // Validation des dates avant soumission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateDates()) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Auto-soumission lors du changement de période
    const periodSelect = document.querySelector('select[name="period"]');
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            if (validateDates()) {
                form.submit();
            }
        });
    }
    
    // Tooltips Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Gestion du responsive pour les graphiques
window.addEventListener('resize', function() {
    Chart.helpers.each(Chart.instances, function(instance) {
        instance.resize();
    });
});

// Fonction pour basculer entre les vues
function toggleView(viewType) {
    const views = ['cards', 'table', 'charts'];
    
    views.forEach(view => {
        const element = document.querySelector(`.${view}-view`);
        if (element) {
            element.style.display = view === viewType ? 'block' : 'none';
        }
    });
    
    // Mettre à jour les boutons actifs
    document.querySelectorAll('.view-toggle').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-view="${viewType}"]`).classList.add('active');
}

// Fonction pour rechercher dans les statistiques
function searchStatistics(query) {
    const cards = document.querySelectorAll('.company-card, .stats-card');
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(query.toLowerCase())) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
@endsection