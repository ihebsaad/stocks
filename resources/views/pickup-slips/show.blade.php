@extends('layouts.admin')

@section('title', 'Détails du bon de ramassage')

@section('styles')
<style>
    .info-card {
        border-left: 4px solid #007bff;
    }
    
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.8em;
    }
    
    .status-badge {
        font-size: 1.1em;
        padding: 0.6em 1em;
    }
    
    .action-buttons {
        gap: 0.5rem;
    }
    
    .print-btn {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .print-btn:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    
    .card-header {
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="float-left">
            <h2>Bon de ramassage: {{ $pickupSlip->reference }}</h2>
        </div>
        <div class="float-right d-flex action-buttons">
            <a class="btn btn-primary" href="{{ route('pickup.index') }}">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a class="btn btn-warning" href="#">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a class="btn btn-success print-btn" href="#" target="_blank">
                <i class="fas fa-print"></i> Imprimer
            </a>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $message }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="row mt-3">
    <!-- Informations générales -->
    <div class="col-md-6">
        <div class="card info-card">
            <div class="card-header">
                <h4><i class="fas fa-info-circle"></i> Informations générales</h4>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Date:</strong></td>
                        <td>{{ date('d/m/Y', strtotime($pickupSlip->date)) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Référence:</strong></td>
                        <td><code>{{ $pickupSlip->reference }}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Société de livraison:</strong></td>
                        <td>{{ $pickupSlip->deliveryCompany->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Statut:</strong></td>
                        <td>
                            <span class="badge badge-{{ $pickupSlip->status === 'pending' ? 'warning' : 
                                                       ($pickupSlip->status === 'completed' ? 'success' : 
                                                       ($pickupSlip->status === 'in_progress' ? 'info' : 'danger')) }} status-badge">
                                {{ ucfirst($pickupSlip->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Nombre de colis:</strong></td>
                        <td>
                            <span class="badge badge-secondary">{{ $pickupSlip->parcels->count() }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Informations de création -->
    <div class="col-md-6">
        <div class="card info-card">
            <div class="card-header">
                <h4><i class="fas fa-user-clock"></i> Informations de création</h4>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Créé par:</strong></td>
                        <td>{{ $pickupSlip->user->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date de création:</strong></td>
                        <td>{{ $pickupSlip->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Dernière modification:</strong></td>
                        <td>{{ $pickupSlip->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques rapides -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-chart-bar"></i> Statistiques</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-primary">{{ $pickupSlip->parcels->count() }}</h3>
                            <small class="text-muted">Total colis</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-success">{{ number_format($pickupSlip->parcels->sum('cod'), 2) }} Dt</h3>
                            <small class="text-muted">Total COD</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-info">{{ $pickupSlip->parcels->groupBy('gov_l')->count() }}</h3>
                            <small class="text-muted">Gouvernorats</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-warning">{{ $pickupSlip->parcels->where('status', 'pending')->count() }}</h3>
                            <small class="text-muted">En attente</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
@if($pickupSlip->status === 'pending')
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-cogs"></i> Actions rapides</h4>
            </div>
            <div class="card-body">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-info" onclick="updateStatus('in_progress')">
                        <i class="fas fa-play"></i> Démarrer le ramassage
                    </button>
                    <button type="button" class="btn btn-success" onclick="updateStatus('completed')">
                        <i class="fas fa-check"></i> Marquer comme terminé
                    </button>
                    <button type="button" class="btn btn-danger" onclick="updateStatus('cancelled')">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Liste des colis -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-boxes"></i> Liste des colis</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
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
                            @forelse($pickupSlip->parcels as $parcel)
                                <tr>
                                    <td><code>{{ $parcel->reference }}</code></td>
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
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Aucun colis associé</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Répartition par gouvernorat -->
@if($pickupSlip->parcels->count() > 0)
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-map-marker-alt"></i> Répartition par gouvernorat</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Gouvernorat</th>
                                <th>Nombre de colis</th>
                                <th>Total COD</th>
                                <th>Pourcentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pickupSlip->parcels->groupBy('gov_l') as $governorate => $parcels)
                                <tr>
                                    <td>{{ $governorate }}</td>
                                    <td>
                                        <span class="badge badge-primary">{{ $parcels->count() }}</span>
                                    </td>
                                    <td>{{ number_format($parcels->sum('cod'), 2) }} Dt</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ ($parcels->count() / $pickupSlip->parcels->count()) * 100 }}%">
                                                {{ number_format(($parcels->count() / $pickupSlip->parcels->count()) * 100, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('footer-scripts')
<script>
    function updateStatus(status) {
        if (confirm('Êtes-vous sûr de vouloir changer le statut de ce bon de ramassage ?')) {
            $.ajax({
                url: '{{ route("pickup.update-status", $pickupSlip->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Erreur: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Erreur lors de la mise à jour du statut');
                }
            });
        }
    }
</script>
@endsection