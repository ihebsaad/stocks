@extends('layouts.admin')
@section('styles')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #10B981 0%, #047857 100%);
            --warning-gradient: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            --danger-gradient: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            --info-gradient: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
            --secondary-gradient: linear-gradient(135deg, #6B7280 0%, #374151 100%);
            --purple-gradient: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
            --indigo-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            --pink-gradient: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            --teal-gradient: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%);
            --orange-gradient: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            --lime-gradient: linear-gradient(135deg, #84cc16 0%, #65a30d 100%);
        }

        .stats-card {
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            animation: slideInUp 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        .stats-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.2) 0%, transparent 70%);
            pointer-events: none;
        }

        .stats-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        .stats-card:hover .stats-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .stats-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: rgba(255,255,255,0.95);
            text-shadow: 0 4px 8px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            display: inline-block;
        }

        .stats-number {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 10px;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            letter-spacing: -1px;
            background: linear-gradient(45deg, rgba(255,255,255,0.9), rgba(255,255,255,0.7));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stats-amount {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.85);
            font-weight: 600;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 1rem;
            text-transform: uppercase;
            font-weight: 700;
            color: rgba(255,255,255,0.95);
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .stats-percentage {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
            font-weight: 500;
        }

        /* Couleurs spécifiques pour chaque statut */
        .stats-card.created { background: var(--info-gradient); }
        .stats-card.updated { background: var(--info-gradient); }
        .stats-card.br_printed { background: var(--purple-gradient); }
        .stats-card.transferred { background: var(--indigo-gradient); }
        .stats-card.in_transit { background: var(--warning-gradient); }
        .stats-card.console_sousse { background: var(--pink-gradient); }
        .stats-card.delivered { background: var(--success-gradient); }
        .stats-card.returned_charged { background: var(--danger-gradient); }
        .stats-card.returned_depot { background: var(--orange-gradient); }
        .stats-card.in_progress { background: var(--teal-gradient); }
        .stats-card.postponed { background: var(--secondary-gradient); }
        .stats-card.collected_tunis { background: var(--lime-gradient); }
        .stats-card.delivered_cash { background: var(--success-gradient); }
        .stats-card.paid { background: var(--success-gradient); }
        .stats-card.definitive_return { background: var(--danger-gradient); }
        .stats-card.return_sender { background: var(--orange-gradient); }
        .stats-card.not_received { background: var(--secondary-gradient); }
        .stats-card.exchange_closed { background: var(--purple-gradient); }

        .stats-card.wha { background: var(--secondary-gradient); }
        .stats-card.other { background: var(--secondary-gradient); }
        .stats-card.inbound { background: var(--secondary-gradient); }
        .stats-card.dated { background: var(--secondary-gradient); }

        /* Grands résumés */
        .summary-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            position: relative;
            overflow: hidden;
        }

        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%);
            pointer-events: none;
        }

        .summary-card.created-summary {
            border-left: 6px solid #3B82F6;
        }

        .summary-card.completed-summary {
            border-left: 6px solid #10B981;
        }

        .summary-card.ongoing-summary {
            border-left: 6px solid #F59E0B;
        }

        .summary-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #667eea;
            text-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .summary-number {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            color: #1f2937;
            letter-spacing: -2px;
        }

        .summary-amount {
            font-size: 1.4rem;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .summary-label {
            font-size: 1.2rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #374151;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .filter-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 40px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .performance-metrics {
            background: var(--primary-gradient);
            color: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
        }

        .metric-item {
            text-align: center;
            padding: 20px;
        }

        .metric-value {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .metric-label {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .chart-container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .company-section {
            margin-bottom: 50px;
        }

        .company-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
            display: flex;
            align-items: center;
        }

        .company-title i {
            margin-right: 15px;
            color: #667eea;
        }

        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 12px 18px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
        }

        .btn {
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .no-data-message {
            text-align: center;
            padding: 80px 20px;
            color: #6b7280;
            font-size: 1.2rem;
        }

        .no-data-message i {
            font-size: 4rem;
            margin-bottom: 25px;
            color: #d1d5db;
        }

        .stats-grid {
            display: grid;
            gap: 25px;
            margin-bottom: 40px;
        }

        .stats-grid.company-2 {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .stats-grid.company-3 {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }

        .stats-grid.summary {
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-card {
                padding: 20px;
            }
            
            .summary-card {
                padding: 30px;
            }
        }

        select{
            display:block
        }



        .period-btn {
            border-radius: 8px !important;
            margin-right: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .period-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .period-btn.active {
            background: var(--primary-gradient) !important;
            border-color: transparent !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .chart-container canvas {
            border-radius: 12px;
        }

        .chart-container h5 {
            color: #1f2937;
            font-weight: 700;
            display: flex;
            align-items: center;
        }

        .chart-container h5 i {
            color: #667eea;
        }

        /* Styles pour les statistiques rapides */
        .chart-container .row.mt-4 .col-md-3 {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 1px solid rgba(59, 130, 246, 0.1);
        }

        .chart-container .row.mt-4 .col-md-3:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .evolution-positive {
            color: #10B981 !important;
        }

        .evolution-negative {
            color: #EF4444 !important;
        }

        .evolution-neutral {
            color: #6B7280 !important;
        }




        
        /* Styles pour les cartes de résumé améliorées */
        .enhanced-summary-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 24px;
            padding: 35px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-soft);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(10px);
        }

        .enhanced-summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea, #764ba2, #10B981, #F59E0B);
            border-radius: 24px 24px 0 0;
        }

        .enhanced-summary-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.03) 0%, transparent 70%);
            pointer-events: none;
            transition: all 0.6s ease;
        }

        .enhanced-summary-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .enhanced-summary-card:hover::after {
            transform: scale(1.1) rotate(10deg);
        }

        .summary-metric {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            margin-bottom: 20px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(248, 250, 252, 0.6));
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            position: relative;
        }

        .enhanced-summary-card .summary-metric:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .summary-metric:last-child {
            margin-bottom: 0;
        }

        .metric-info {
            display: flex;
            align-items: center;
        }

        .metric-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-right: 20px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .metric-icon.total { background: var(--primary-gradient); }
        .metric-icon.amount { background: var(--success-gradient); }
        .metric-icon.delivery { background: var(--info-gradient); }
        .metric-icon.return { background: var(--warning-gradient); }

        .metric-details h6 {
            margin: 0 0 5px 0;
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .enhanced-summary-card .metric-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
            letter-spacing: -0.5px;
        }
 
    </style>
@endsection

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistiques des Colis
                </h1>
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
                    <input type="hidden" name="period" value="{{ $filters['period'] ?? 'weekly' }}">

                </div>
                <div class="col-md-4 pr-4">
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
                <!--<div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="Colis Créé" {{ ($filters['status'] ?? '') == 'Colis Créé' ? 'selected' : '' }}>Colis Créé</option>
                        <option value="En transit" {{ ($filters['status'] ?? '') == 'En transit' ? 'selected' : '' }}>En transit</option>
                        <option value="Colis livré" {{ ($filters['status'] ?? '') == 'Colis livré' ? 'selected' : '' }}>Colis livré</option>
                    </select>
                </div>-->
                <div class="col-md-4 pl-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>
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

    <!-- Statistiques par société -->
    @if(isset($filters['delivery_company_id']) && $filters['delivery_company_id'] != '')
        <!-- Affichage pour une société spécifique -->
        @if(!empty($companyStats['status_stats']))
            <div class="company-section">
                <h2 class="company-title">
                    <i class="fas fa-building"></i>
                    {{ $companyStats['company_name'] }}
                </h2>
                
                <div class="stats-grid company-{{ $filters['delivery_company_id'] }}">
                    @foreach($companyStats['status_stats'] as $status)
                        <div class="stats-card text-center {{ $status['key'] }}">
                            <div class="stats-icon">
                                <i class="{{ $status['icon'] }}"></i>
                            </div>
                            <div class="stats-label">{{ $status['label'] }}</div>
                            <div class="stats-number">{{ $status['count'] }}</div>
                            <div class="stats-amount">{{ number_format($status['amount'], 0) }} TND</div>
                            <div class="stats-percentage">{{ $status['percentage'] }}%</div>
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
        <!-- Affichage général - Résumé en 3 grandes cartes -->
        <div class="company-section">
            <h2 class="company-title">
                <i class="fas fa-chart-pie"></i>
                Résumé Global
            </h2>
            
            <div class="stats-grid summary">
                <!-- Créés -->
                <div class="summary-card text-center created-summary">
                    <div class="summary-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="summary-label">Créés</div>
                    <div class="summary-number">{{ $mainStats['created_count'] }}</div>
                    <div class="summary-amount">{{ number_format($mainStats['created_amount'], 0) }} TND</div>
                </div>
                
                <!-- En cours -->
                <div class="summary-card text-center ongoing-summary">
                    <div class="summary-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="summary-label">En cours</div>
                    <div class="summary-number">{{ $mainStats['in_transit_count'] + $mainStats['pending_count'] + $mainStats['other_count'] }}</div>
                    <div class="summary-amount">{{ number_format($mainStats['in_transit_amount'] + $mainStats['pending_amount'] + $mainStats['other_amount'], 0) }} TND</div>
                </div>

                <!-- Terminés -->
                <div class="summary-card text-center completed-summary">
                    <div class="summary-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="summary-label">Terminés</div>
                    <div class="summary-number">{{ $mainStats['delivered_count'] }}</div>
                    <div class="summary-amount">{{ number_format($mainStats['delivered_amount'], 0) }} TND</div>
                </div>
                

            </div>
        </div>

        <!-- Statistiques détaillées par société -->
        @if(!empty($companyStatistics))
            @foreach($companyStatistics as $companyStat)
                <div class="company-section">
                    <h2 class="company-title">
                        <i class="fas fa-building"></i>
                        {{ $companyStat['company_name'] }}
                    </h2>
                    
                    <div class="stats-grid company-{{ $companyStat['company_id'] }}">
                        @foreach($companyStat['status_stats'] as $status)
                            <div class="stats-card text-center {{ $status['key'] }}">
                                <div class="stats-icon">
                                    <i class="{{ $status['icon'] }}"></i>
                                </div>
                                <div class="stats-label">{{ $status['label'] }}</div>
                                <div class="stats-number">{{ $status['count'] }}</div>
                                <div class="stats-amount">{{ number_format($status['amount'], 0) }} TND</div>
                                <div class="stats-percentage">{{ $status['percentage'] }}%</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    @endif

    <!-- Graphique
    <div class="row">
        <div class="col-md-12">
            <div class="chart-container">
                <h5 class="mb-3">Résumé Total</h5>
                <div class="row text-center">
                    <div class="col-md-6 mb-3">
                        <div class="h4 text-primary">{{ $mainStats['total_parcels'] ?? 0 }}</div>
                        <div class="text-muted">Total colis</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="h4 text-success">{{ number_format($mainStats['total_amount'] ?? 0, 0) }} TND</div>
                        <div class="text-muted">Montant total</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="h4 text-info">{{ number_format($performanceMetrics['delivery_rate'], 1) }}%</div>
                        <div class="text-muted">Taux de livraison</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="h4 text-warning">{{ number_format($performanceMetrics['return_rate'], 1) }}%</div>
                        <div class="text-muted">Taux de retour</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 -->
        <div class="row">
            <div class="col-12">
                <div class="enhanced-summary-card animated-card">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-chart-pie"></i>
                            Résumé Total
                        </h3>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="summary-metric">
                                <div class="metric-info">
                                    <div class="metric-icon total">
                                        <i class="fas fa-boxes"></i>
                                    </div>
                                    <div class="metric-details">
                                        <h6>Total Colis</h6>
                                        <p class="metric-value">{{ $mainStats['total_parcels'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="summary-metric">
                                <div class="metric-info">
                                    <div class="metric-icon amount">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                    <div class="metric-details">
                                        <h6>Chiffre d'Affaires</h6>
                                        <p class="metric-value">{{ number_format($mainStats['total_amount'] ?? 0, 0) }} TND</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="summary-metric">
                                <div class="metric-info">
                                    <div class="metric-icon delivery">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="metric-details">
                                        <h6>Taux de Livraison</h6>
                                        <p class="metric-value">{{ number_format($performanceMetrics['delivery_rate'], 1) }}%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="summary-metric">
                                <div class="metric-info">
                                    <div class="metric-icon return">
                                        <i class="fas fa-undo-alt"></i>
                                    </div>
                                    <div class="metric-details">
                                        <h6>Taux de Retour</h6>
                                        <p class="metric-value">{{ number_format($performanceMetrics['return_rate'], 1) }}%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="row">
        <div class="col-md-12">
            <div class="chart-container">
                <h5 class="mb-4">
                    <i class="fas fa-chart-line me-2"></i>
                    Évolution des Colis et Chiffre d'Affaires
                </h5>
                
                <!-- Sélecteur de période -->
                <div class="mb-3">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary period-btn" data-period="daily">
                            <i class="fas fa-calendar-day me-1"></i>
                            Journalier
                        </button>
                        <button type="button" class="btn btn-outline-primary period-btn active" data-period="weekly">
                            <i class="fas fa-calendar-week me-1"></i>
                            Hebdomadaire
                        </button>
                        <button type="button" class="btn btn-outline-primary period-btn" data-period="monthly">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Mensuel
                        </button>
                    </div>
                </div>
                
                <!-- Conteneur du graphique -->
                <div style="position: relative; height: 400px;">
                    <canvas id="lineChart"></canvas>
                </div>
                
                <!-- Statistiques rapides sous le graphique -->
                <div class="row mt-4">
                    <div class="col-md-3 text-center">
                        <div class="small text-muted">Période actuelle</div>
                        <div class="h6 text-primary" id="currentPeriodParcels">-</div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="small text-muted">CA période actuelle</div>
                        <div class="h6 text-success" id="currentPeriodAmount">-</div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="small text-muted">Évolution colis</div>
                        <div class="h6" id="parcelsEvolution">-</div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="small text-muted">Évolution CA</div>
                        <div class="h6" id="amountEvolution">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

// Animation des compteurs
function animateCounters() {
    const counters = document.querySelectorAll('.stats-number, .summary-number');
    
    counters.forEach((counter, index) => {
        const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        // Délai d'animation échelonné
        setTimeout(() => {
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    counter.textContent = target.toLocaleString();
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current).toLocaleString();
                }
            }, 16);
        }, index * 100);
    });
}

// Validation des dates
function validateDates() {
    const dateFrom = document.querySelector('input[name="date_from"]').value;
    const dateTo = document.querySelector('input[name="date_to"]').value;
    
    if (dateFrom && dateTo) {
        const fromDate = new Date(dateFrom);
        const toDate = new Date(dateTo);
        
        if (fromDate > toDate) {
            alert('La date de début doit être antérieure à la date de fin');
            return false;
        }
    }
    
    return true;
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Animer les compteurs
    setTimeout(animateCounters, 500);
    
    // Validation du formulaire
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateDates()) {
                e.preventDefault();
                return false;
            }
        });
    }
});


    let lineChart;
    let currentPeriod = 'weekly';

    // Fonction pour initialiser le graphique
    function initializeChart() {
        const ctx = document.getElementById('lineChart').getContext('2d');
        const periodData = @json($periodStats);
        
        lineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: periodData.map(item => item.formatted_date),
                datasets: [
                    {
                        label: 'Nombre de colis',
                        data: periodData.map(item => item.total_parcels),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Chiffre d\'affaires (TND)',
                        data: periodData.map(item => item.total_amount),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 16,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 14
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.datasetIndex === 1) {
                                    label += new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' TND';
                                } else {
                                    label += new Intl.NumberFormat('fr-FR').format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Période',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Nombre de colis',
                            color: '#3B82F6',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            color: '#3B82F6',
                            font: {
                                size: 12
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Chiffre d\'affaires (TND)',
                            color: '#10B981',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            color: '#10B981',
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR').format(value);
                            }
                        }
                    }
                },
                elements: {
                    point: {
                        hoverBackgroundColor: '#ffffff'
                    }
                }
            }
        });
        
        // Mettre à jour les statistiques rapides
        updateQuickStats(periodData);
    }

    // Fonction pour mettre à jour les statistiques rapides
    function updateQuickStats(data) {
        if (data.length === 0) return;
        
        const currentPeriodData = data[data.length - 1];
        const previousPeriodData = data.length > 1 ? data[data.length - 2] : null;
        
        // Valeurs actuelles
        document.getElementById('currentPeriodParcels').textContent = currentPeriodData.total_parcels.toLocaleString();
        document.getElementById('currentPeriodAmount').textContent = currentPeriodData.total_amount.toLocaleString() + ' TND';
        
        // Évolutions
        if (previousPeriodData) {
            const parcelsEvolution = currentPeriodData.total_parcels - previousPeriodData.total_parcels;
            const amountEvolution = currentPeriodData.total_amount - previousPeriodData.total_amount;
            
            updateEvolutionElement('parcelsEvolution', parcelsEvolution);
            updateEvolutionElement('amountEvolution', amountEvolution, ' TND');
        }
    }

    // Fonction pour mettre à jour un élément d'évolution
    function updateEvolutionElement(elementId, value, suffix = '') {
        const element = document.getElementById(elementId);
        const icon = value > 0 ? '↗' : value < 0 ? '↘' : '→';
        const className = value > 0 ? 'evolution-positive' : value < 0 ? 'evolution-negative' : 'evolution-neutral';
        
        element.textContent = `${icon} ${Math.abs(value).toLocaleString()}${suffix}`;
        element.className = `h6 ${className}`;
    }

    // Fonction pour changer de période
    function changePeriod(newPeriod) {
        currentPeriod = newPeriod;
        
        // Mettre à jour les boutons
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-period="${newPeriod}"]`).classList.add('active');
        
        // Recharger les données avec la nouvelle période
        const currentFilters = new URLSearchParams(window.location.search);
        currentFilters.set('period', newPeriod);
        
        // Vous pouvez soit faire un appel AJAX, soit recharger la page
        // Pour l'exemple, nous rechargeons la page
        window.location.search = currentFilters.toString();
    }

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser le graphique
        setTimeout(initializeChart, 500);
        
        // Gestionnaires d'événements pour les boutons de période
        document.querySelectorAll('.period-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const period = this.getAttribute('data-period');
                changePeriod(period);
            });
        });
        
        // Animation d'entrée du graphique
        setTimeout(() => {
            const chartContainer = document.querySelector('#lineChart').parentElement;
            chartContainer.style.opacity = '0';
            chartContainer.style.transform = 'translateY(20px)';
            chartContainer.style.transition = 'all 0.6s ease';
            
            setTimeout(() => {
                chartContainer.style.opacity = '1';
                chartContainer.style.transform = 'translateY(0)';
            }, 100);
        }, 200);
    });
</script>
@endsection