@extends('layouts.admin')

@section('title', 'Gestion des commandes')

@section('styles')
<style>
    .status-badge {
        display: inline-block;
        padding: 0.25em 0.6em;
        font-size: 0.75em;
        font-weight: 700;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }
    .status-draft { background-color: #6c757d; color: white; }
    .status-pending { background-color: #ffc107; color: black; }
    .status-pickup { background-color: #17a2b8; color: white; }
    .status-no_response { background-color: #dc3545; color: white; }
    .status-cancelled { background-color: #6c757d; color: white; }
    .status-in_delivery { background-color: #007bff; color: white; }
    .status-completed { background-color: #28a745; color: white; }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Liste des Commandes</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle commande
            </a>
        </div>
    </div>
    
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('orders.index') }}" method="GET" class="form-inline">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                            <select name="status" class="form-control">
                                <option value="">Tous les statuts</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="pickup" {{ request('status') == 'pickup' ? 'selected' : '' }}>En ramassage</option>
                                <option value="no_response" {{ request('status') == 'no_response' ? 'selected' : '' }}>Client ne répond plus</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                                <option value="in_delivery" {{ request('status') == 'in_delivery' ? 'selected' : '' }}>En livraison</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminée</option>
                            </select>
                            <button type="submit" class="btn btn-outline-secondary">Filtrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Service</th>
                            <th>Société de livraison</th>
                            <th>Statut</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($orders->count() > 0)
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>
                                        @if($order->client)
                                            {{ $order->client->full_name }}<br>
                                            <small>{{ $order->client->phone }}</small>
                                        @else
                                            <span class="text-muted">Non défini</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->service_type)
                                            {{ $order->service_type == 'delivery' ? 'Livraison' : 'Échange' }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->deliveryCompany)
                                            {{ $order->deliveryCompany->name }}
                                            @if($order->free_delivery)
                                                <span class="badge bg-success">Gratuite</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $order->status }}">
                                            @switch($order->status)
                                                @case('draft')
                                                    Brouillon
                                                    @break
                                                @case('pending')
                                                    En attente
                                                    @break
                                                @case('pickup')
                                                    En ramassage
                                                    @break
                                                @case('no_response')
                                                    Client ne répond plus
                                                    @break
                                                @case('cancelled')
                                                    Annulée
                                                    @break
                                                @case('in_delivery')
                                                    En livraison
                                                    @break
                                                @case('completed')
                                                    Terminée
                                                    @break
                                                @default
                                                    {{ $order->status }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">Aucune commande trouvée</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection