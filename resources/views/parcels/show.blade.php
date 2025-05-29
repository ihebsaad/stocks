@extends('layouts.admin')

<style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #64748b;
        --success-color: #059669;
        --warning-color: #d97706;
        --danger-color: #dc2626;
        --info-color: #0891b2;
        --light-bg: #f8fafc;
        --border-color: #e2e8f0;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .main-container {
        background: white;
        margin: 2rem auto;
        max-width: 1200px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
    }

    .header-section {
        background: linear-gradient(135deg, #00a69c 0%, #003936 100%);
        color: white;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .header-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(50%, -50%);
    }

    .parcel-id {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header-meta {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .content-section {
        padding: 2rem;
    }

    .info-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        height: 100%;
    }

    .info-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .info-card h5 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 500;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 120px;
    }

    .info-value {
        font-weight: 600;
        color: var(--text-primary);
        text-align: right;
        flex: 1;
        word-break: break-word;
    }

    .btn-back {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        color: white;
        text-decoration: none;
    }

    .company-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-block;
    }

    .bg-1 { background-color: #0da598; color: white; }
    .bg-2 { background-color: #ef6f28; color: white; }
    .bg-3 { background-color: #227ac2; color: white; }
    .bg-4 { background-color: #6c757d; color: white; }
    .bg-5 { background-color: #fd9883; color: white; }

    .timeline-container {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 0.5rem;
    }

    .timeline-item {
        position: relative;
        padding-left: 2rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
    }

    .timeline-item:before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: -1.5rem;
        width: 2px;
        background: linear-gradient(to bottom, #3b82f6, #e2e8f0);
    }

    .timeline-item:last-child:before {
        bottom: 0;
    }

    .timeline-dot {
        position: absolute;
        left: 0;
        top: 0;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: var(--primary-color);
        border: 3px solid white;
        box-shadow: 0 0 0 3px var(--primary-color);
    }

    .timeline-content {
        background: #f8fafc;
        border-radius: 8px;
        padding: 1rem;
        border-left: 3px solid var(--primary-color);
    }

    .timeline-date {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }

    .timeline-status {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .timeline-user {
        font-size: 0.875rem;
        color: var(--text-secondary);
        font-style: italic;
    }

    .cod-amount {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--success-color);
    }

    sup, sub {
        vertical-align: baseline;
        position: relative;
        top: -0.4em;
    }
    
    sub { 
        top: 0.4em; 
    }

    .content{
        padding-top:0px!important;
        padding-bottom:0px!important;
    }
    @media (max-width: 768px) {
        .main-container {
            margin: 1rem;
            border-radius: 12px;
        }
        
        .header-section {
            padding: 1.5rem;
        }
        
        .parcel-id {
            font-size: 2rem;
        }
        
        .content-section {
            padding: 1rem;
        }
        
        .info-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .info-label {
            min-width: auto;
        }

        .info-value {
            text-align: left;
        }
    }

    .fade-in {
        animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

@section('content')
<div class="main-container fade-in">
    <!-- Header Section -->
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="parcel-id">{{ $parcel->reference }}</div>
                <div class="header-meta">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Créé le {{ $parcel->created_at->format('d/m/Y à H:i') }}
                </div>
            </div>
            <a href="{{ route('parcels.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Content Section -->
    <div class="content-section">
        <div class="row">
            <!-- Informations Client -->
            <div class="col-lg-4 col-md-6">
                <div class="info-card">
                    <h5>
                        <i class="fas fa-user"></i>
                        Informations Client
                    </h5>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-user-circle"></i>
                            Client
                        </span>
                        <span class="info-value">{{ $parcel->nom_client }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-phone"></i>
                            Téléphone
                        </span>
                        <span class="info-value">{{ $parcel->tel_l }}</span>
                    </div>
                    @if($parcel->tel2_l)
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-phone-alt"></i>
                            Tél 2
                        </span>
                        <span class="info-value">{{ $parcel->tel2_l }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-map-marker-alt"></i>
                            Ville
                        </span>
                        <span class="info-value">{{ $parcel->ville_cl }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-map"></i>
                            Délégation
                        </span>
                        <span class="info-value">{{ $parcel->gov_l }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-home"></i>
                            Adresse
                        </span>
                        <span class="info-value">{{ $parcel->adresse_l }}</span>
                    </div>
                </div>
            </div>

            <!-- Détails Colis -->
            <div class="col-lg-4 col-md-6">
                <div class="info-card">
                    <h5>
                        <i class="fas fa-box"></i>
                        Détails du Colis
                    </h5>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-building"></i>
                            Transporteur
                        </span>
                        <span class="info-value">
                            <span class="company-badge bg-{{ $parcel->company->id }}">
                                {{ ucfirst($parcel->company->name) }}
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-tag"></i>
                            Libellé
                        </span>
                        <span class="info-value">{{ $parcel->libelle }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-cubes"></i>
                            Nb pièces
                        </span>
                        <span class="info-value">{{ $parcel->nb_piece }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-money-bill-wave"></i>
                            COD
                        </span>
                        <span class="info-value cod-amount">{{ $parcel->cod }} <sup>TND</sup></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-truck"></i>
                            Service
                        </span>
                        <span class="info-value">
                            {!! $parcel->service == 'Livraison' ? '<strong>Livraison</strong>' : '<em>Échange</em>' !!}
                        </span>
                    </div>
                    @if($parcel->remarque)
                    <div class="info-row">
                        <span class="info-label">
                            <i class="fas fa-comment"></i>
                            Remarque
                        </span>
                        <span class="info-value">{{ $parcel->remarque }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Historique -->
            <div class="col-lg-4 col-md-12">
                <div class="info-card">
                    <h5>
                        <i class="fas fa-history"></i>
                        Historique du Colis
                    </h5>
                    <div class="timeline-container">
                        @forelse($historiques as $history)
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">{{ $history->created_at->format('d/m/Y à H:i') }}</div>
                                <div class="timeline-status">
                                    @if($history->old_status)
                                        {{ $statusOptions[$history->old_status] ?? $history->old_status }} → 
                                    @endif
                                    {{ $statusOptions[$history->new_status] ?? $history->new_status }}
                                </div>
                                @if($history->comment)
                                    <div class="mb-2">{{ $history->comment }}</div>
                                @endif
                                <div class="timeline-user">par {{ $history->user->name ?? 'API' }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted">
                            <i class="fas fa-info-circle mb-2"></i>
                            <p>Aucun historique disponible</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection