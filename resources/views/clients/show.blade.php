@extends('layouts.admin')

@section('title', 'Détails client - ' . $client->full_name)

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
    .client-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 30px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    .client-avatar-large {
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 28px;
        margin-right: 20px;
    }
    .client-info-header {
        display: flex;
        align-items: center;
    }
    .client-details-header h2 {
        margin: 0;
        font-size: 2.2rem;
        font-weight: 700;
    }
    .client-details-header .client-id {
        opacity: 0.8;
        font-size: 1rem;
        margin-top: 5px;
    }
    .client-details-header .member-since {
        opacity: 0.7;
        font-size: 0.9rem;
        margin-top: 5px;
    }
    .stats-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #f0f0f0;
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-2px);
    }
    .stats-card .icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 15px;
    }
    .stats-card h3 {
        font-size: 2rem;
        font-weight: bold;
        margin: 0;
        color: #333;
    }
    .stats-card p {
        margin: 5px 0 0 0;
        color: #666;
        font-size: 0.9rem;
    }
    .info-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #f0f0f0;
    }
    .info-card h5 {
        color: #333;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: #555;
    }
    .info-value {
        color: #333;
    }
    .phone-badge {
        background: #e3f2fd;
        color: #1976d2;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-right: 5px;
        display: inline-block;
    }
    .location-badge {
        background: #f3e5f5;
        color: #7b1fa2;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-right: 5px;
    }
    .orders-table-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #f0f0f0;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-processing { background: #d4edda; color: #155724; }
    .status-shipped { background: #cce5ff; color: #004085; }
    .status-delivered { background: #d1ecf1; color: #0c5460; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    .order-amount {
        font-weight: 600;
        color: #28a745;
        font-size: 1.1rem;
    }
    .chart-container {
        position: relative;
        height: 300px;
        margin-top: 20px;
    }
    .btn-back {
        background: #6c757d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-back:hover {
        background: #5a6268;
        color: white;
        text-decoration: none;
    }
    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }
    .btn-action {
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-edit {
        background: #007bff;
        color: white;
    }
    .btn-edit:hover {
        background: #0056b3;
        color: white;
        text-decoration: none;
    }
    .btn-order {
        background: #28a745;
        color: white;
    }
    .btn-order:hover {
        background: #1e7e34;
        color: white;
        text-decoration: none;
    }


    /* Styles pour les codes promos */
    .promo-item {
        transition: all 0.3s ease;
    }

    .promo-item:hover {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .free-product {
        background-color: #f8f9fa;
        border: 2px dashed #28a745;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .free-product .badge {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
    .promos{
        padding: 20px 20px 20px 20px;
        border-radius: 10px;
        margin-top: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Bouton retour -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('clients.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour à la liste des clients
            </a>
        </div>
    </div>

    <!-- En-tête client -->
    <div class="client-header">
        <div class="client-info-header">
            <div class="client-avatar-large">
                {{ strtoupper(substr($client->first_name, 0, 1) . substr($client->last_name, 0, 1)) }}
            </div>
            <div class="client-details-header">
                <h2>{{ $client->full_name }}</h2>
                <div class="client-id">Client #{{ $client->id }}</div>
                <div class="member-since">
                    <i class="fas fa-calendar-alt"></i> Membre depuis le {{ $client->created_at->format('d/m/Y') }}
                </div>
                <!--
                <div class="action-buttons">
                    <a href="#" class="btn-action btn-edit">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="#" class="btn-action btn-order">
                        <i class="fas fa-plus"></i> Nouvelle commande
                    </a>
                </div>
                -->
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon" style="background: #e3f2fd; color: #1976d2;">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3>{{ $totalOrders }}</h3>
                <p>Total commandes</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon" style="background: #e8f5e8; color: #4caf50;">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h3>{{ number_format($totalAmount, 2) }} DT</h3>
                <p>Montant total</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon" style="background: #fff3e0; color: #ff9800;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>{{ number_format($avgOrderAmount, 2) }} DT</h3>
                <p>Panier moyen</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="icon" style="background: #f3e5f5; color: #9c27b0;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3>{{ $lastOrderDate ? $lastOrderDate->format('d/m/Y') : 'N/A' }}</h3>
                <p>Dernière commande</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations client -->
        <div class="col-md-4">
            <div class="info-card">
                <h5><i class="fas fa-user"></i> Informations personnelles</h5>
                <div class="info-row">
                    <span class="info-label">Prénom:</span>
                    <span class="info-value">{{ $client->first_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nom:</span>
                    <span class="info-value">{{ $client->last_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Téléphones:</span>
                    <div>
                        <span class="phone-badge">
                            <i class="fas fa-phone"></i> {{ $client->phone }}
                        </span>
                        @if($client->phone2)
                            <br><span class="phone-badge">
                                <i class="fas fa-mobile-alt"></i> {{ $client->phone2 }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="info-card">
                <h5><i class="fas fa-map-marker-alt"></i> Localisation</h5>
                <div class="info-row">
                    <span class="info-label">Ville:</span>
                    <span class="location-badge">{{ $client->city }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Délégation:</span>
                    <span class="info-value">{{ $client->delegation }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Adresse:</span>
                    <span class="info-value">{{ $client->address ?? 'Non renseignée' }}</span>
                </div>
                @if($client->postal_code)
                <div class="info-row">
                    <span class="info-label">Code postal:</span>
                    <span class="info-value">{{ $client->postal_code }}</span>
                </div>
                @endif
            </div>

            <!-- Évolution des commandes -->
            <div class="info-card">
                <h5><i class="fas fa-chart-area"></i> Évolution des commandes</h5>
                <div class="chart-container">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Historique des commandes -->
        <div class="col-md-8">
            <div class="orders-table-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5><i class="fas fa-history"></i> Historique des commandes</h5>
                    <span class="badge badge-secondary">{{ $totalOrders }} commande(s)</span>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover" id="orders-table">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th>N° Commande</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Montant</th>
                                <th>Livraison</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($client->orders as $order)
                            <tr>
                                <td>
                                    <strong>
                                        <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-primary mr-1 mb-1" title="Voir">#{{ $order->id }}</a>
                                    </strong>
                                    @if($order->notes)
                                        <br><small class="text-muted">{{ Str::limit($order->notes, 30) }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $order->created_at->format('d/m/Y') }}
                                    <br><small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'pending' => 'status-pending',
                                            'processing' => 'status-processing',
                                            'shipped' => 'status-shipped',
                                            'delivered' => 'status-delivered',
                                            'cancelled' => 'status-cancelled'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'En attente',
                                            'processing' => 'En traitement',
                                            'shipped' => 'Expédiée',
                                            'delivered' => 'Livrée',
                                            'cancelled' => 'Annulée'
                                        ];
                                    @endphp
                                    <span class="status-badge {{ $statusClasses[$order->status] ?? 'status-pending' }}">
                                        {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="order-amount">{{ number_format($order->total, 2) }} DT</div>
                                    @if($order->discount > 0)
                                        <small class="text-success">-{{ number_format($order->discount, 2) }} DT</small>
                                    @endif
                                </td>
                                <td>
                                    @if($order->deliveryCompany)
                                        <strong>{{ $order->deliveryCompany->name }}</strong><br>
                                        <small class="text-muted">{{ number_format($order->delivery_cost, 2) }} DT</small>
                                    @else
                                        <span class="text-muted">Non assignée</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-outline-primary" title="Voir détails">#{{ $order->id }}</a>
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card mt-4 mb-2"  >
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Codes Promos</h6>
                    <button type="button" class="btn btn-success btn-sm"  id="add-promo-btn"  >
                        <i class="fas fa-plus"></i> Créer
                    </button>
                </div>
                <div class="card-body">
 
                    @if($client->promoCodes->count() > 0)
                        <div class="promo-codes-list">
                            @foreach($client->promoCodes as $promo)
                                <div class="promo-item mb-2 p-3 border rounded  bg-light">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <strong class="me-2">{{ $promo->code }}</strong>
                                            </div>
                                                                
                                            <div class="text-muted mb-1">
                                                @if($promo->type == 'percentage')
                                                                        <i class="fas fa-percentage"></i> Remise de {{ $promo->value }}%
                                                @elseif($promo->type == 'fixed_amount')
                                                                        <i class="fas fa-money-bill"></i> Remise de {{ $promo->value }} TND
                                                @elseif($promo->type == 'free_product')
                                                                        <i class="fas fa-gift"></i> Produit gratuit : {{ $promo->product->name ?? 'Produit non trouvé' }}
                                                @endif
                                            </div>
                                                                
                                            <div class="text-muted small">
                                                <i class="fas fa-calendar"></i> 
                                                Expire le : {{ $promo->expires_at ? $promo->expires_at->format('d/m/Y') : 'Jamais' }}
                                            </div>
                                                                
                                            <div class="mt-2">
                                                @if($promo->expires_at && $promo->expires_at->isPast())
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times"></i> Expiré
                                                    </span>
                                                @elseif($promo->is_used)
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-exclamation-triangle"></i> Utilisé
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> Valide
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun code promo disponible pour ce client</p>
                        </div>
                    @endif
                                        
                </div>            
            </div>            
        </div>
    </div>
</div>



<!-- Modal pour créer un code promo -->
<div class="modal fade" id="createPromoModal" tabindex="-1" aria-labelledby="createPromoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createPromoForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createPromoModalLabel">Créer un code promo</h5>
                    <button type="button" class="btn btn-close" data-dismiss="modal" aria-label="Close">X</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="client_id" value="{{ $client->id ?? '' }}">
                                            
                    <div class="mb-3">
                        <label for="promo_code" class="form-label">Code promo *</label>
                        <input type="text" class="form-control" id="promo_code" name="code" required>
                        <div class="form-text">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="generateCodeBtn">
                                Générer automatiquement
                            </button>
                        </div>
                    </div>
                                            
                    <div class="mb-3">
                        <label for="promo_type" class="form-label">Type *</label>
                        <select class="form-control" id="promo_type" name="type" required>
                            <option value="">Sélectionner...</option>
                            <option value="percentage">Pourcentage</option>
                            <option value="fixed_amount">Montant fixe</option>
                            <option value="free_product">Produit gratuit</option>
                        </select>
                    </div>
                                            
                    <div class="mb-3" id="value_container">
                        <label for="promo_value" class="form-label">Valeur *</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="promo_value" name="value" step="0.01" min="0">
                                <span class="input-group-text" id="value_unit">TND</span>
                            </div>
                    </div>
                                            
                    <div class="mb-3" id="product_container" style="display: none;">
                        <label for="promo_product" class="form-label">Produit gratuit *</label>
                        <select class="form-control" id="promo_product" name="product_id">
                            <option value="">Sélectionner un produit...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->reference }})</option>
                                @endforeach
                        </select>
                    </div>
                                            
                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Date d'expiration</label>
                        <input type="date" class="form-control" id="expires_at" name="expires_at" 
                            min="{{ date('Y-m-d') }}">
                        <div class="form-text">Laisser vide pour aucune expiration</div>
                    </div>
                                            
                    <input type="hidden" name="apply_immediately" value="0">
                    <!--
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="apply_immediately" name="apply_immediately" value="1">
                        <label class="form-check-label" for="apply_immediately">
                            Appliquer immédiatement à cette commande
                        </label>
                    </div>-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary"  id="add-code">Créer le code promo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(function() {
    // Initialiser DataTable pour les commandes
    $('#orders-table').DataTable({
        order: [[1, 'desc']],
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
        },
        responsive: true
    });

    // Graphique d'évolution des commandes
    const ctx = document.getElementById('ordersChart').getContext('2d');
    
    // Données pour le graphique (à adapter selon vos besoins)
    const monthlyData = @json($monthlyOrdersData);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Commandes',
                data: monthlyData.map(item => item.count),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Animations au chargement
    $('.stats-card').each(function(index) {
        $(this).delay(index * 100).fadeIn();
    });
});


    $('#add-promo-btn').click(function() {
        addPromo();
    });


function addPromo() {
    // Ouvrir la modal pour ajouter un code promo
    const promoModal = new bootstrap.Modal(document.getElementById('createPromoModal'));
    promoModal.show();
}
        
 
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du type de promo
    document.getElementById('promo_type').addEventListener('change', function() {
        const valueContainer = document.getElementById('value_container');
        const productContainer = document.getElementById('product_container');
        const valueUnit = document.getElementById('value_unit');
        const promoValue = document.getElementById('promo_value');
        
        if (this.value === 'free_product') {
            valueContainer.style.display = 'none';
            productContainer.style.display = 'block';
            promoValue.required = false;
            document.getElementById('promo_product').required = true;
        } else {
            valueContainer.style.display = 'block';
            productContainer.style.display = 'none';
            promoValue.required = true;
            document.getElementById('promo_product').required = false;
            
            if (this.value === 'percentage') {
                valueUnit.textContent = '%';
                promoValue.max = 100;
            } else {
                valueUnit.textContent = 'TND';
                promoValue.removeAttribute('max');
            }
        }
    });
    
    // Générateur de code automatique
    document.getElementById('generateCodeBtn').addEventListener('click', function() {
        const clientName = '{{ $order->client->first_name ?? "CLIENT" }}';
        const timestamp = Date.now().toString().slice(-6);
        const randomCode = clientName.substring(0, 3).toUpperCase() + timestamp;
        document.getElementById('promo_code').value = randomCode;
    });   
    
 
});

 
function initializeCurrentPromo() {
    const promoCodeId = $('#promo_code_id').val();
    if (promoCodeId) {
        const promoButton = $(`.apply-promo-btn[data-promo-id="${promoCodeId}"], .remove-promo-btn[data-promo-id="${promoCodeId}"]`);
        if (promoButton.length > 0) {
            currentPromoData = {
                id: promoCodeId,
                type: promoButton.data('promo-type'),
                value: promoButton.data('promo-value'),
                product_id: promoButton.data('promo-product-id')
            };
        } else {
            // AJOUTER: Fallback pour récupérer depuis les champs hidden
            const typeField = $('#promo_code_type').val();
            const valueField = $('#promo_code_value').val();
            
            if (typeField) {
                currentPromoData = {
                    id: promoCodeId,
                    type: typeField,
                    value: parseFloat(valueField || 0),
                    product_id: null
                };
            }
        }
    }
}


// Gestion de la création de code promo
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du type de promo
    const promoTypeSelect = document.getElementById('promo_type');
    if (promoTypeSelect) {
        promoTypeSelect.addEventListener('change', function() {
            const valueContainer = document.getElementById('value_container');
            const productContainer = document.getElementById('product_container');
            const valueUnit = document.getElementById('value_unit');
            const promoValue = document.getElementById('promo_value');
            
            if (this.value === 'free_product') {
                valueContainer.style.display = 'none';
                productContainer.style.display = 'block';
                promoValue.required = false;
                document.getElementById('promo_product').required = true;
            } else {
                valueContainer.style.display = 'block';
                productContainer.style.display = 'none';
                promoValue.required = true;
                document.getElementById('promo_product').required = false;
                
                if (this.value === 'percentage') {
                    valueUnit.textContent = '%';
                    promoValue.max = 100;
                } else {
                    valueUnit.textContent = 'TND';
                    promoValue.removeAttribute('max');
                }
            }
        });
    }
    
    // Générateur de code automatique
    const generateCodeBtn = document.getElementById('generateCodeBtn');
    if (generateCodeBtn) {
        generateCodeBtn.addEventListener('click', function() {
            const timestamp = Date.now().toString().slice(-6);
            const randomCode = 'PROMO' + timestamp;
            document.getElementById('promo_code').value = randomCode;
        });
    }
    
    // Gestion de la soumission du formulaire de création de code promo
    const createPromoForm = document.getElementById('createPromoForm');
    if (createPromoForm) {
        createPromoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Afficher un indicateur de chargement
            const submitBtn = document.getElementById('add-code');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Création en cours...';
            submitBtn.disabled = true;
            
            fetch('/promo-codes', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    if (response.status === 422) {
                        let messages = '';
                        for (const key in data.errors) {
                            messages += data.errors[key].join('\n') + '\n';
                        }
                        alert('Erreurs de validation :\n' + messages);
                    } else {
                        alert('Erreur: ' + (data.message || 'Erreur inconnue'));
                    }
                    throw new Error('Erreur http ' + response.status);
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    // Fermer la modal
                    $('#createPromoModal').modal('hide');
                    // Recharger la page pour afficher le nouveau code
                    location.reload();
                } else {
                    alert('Erreur lors de la création du code promo: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
            })
            .finally(() => {
                // Restaurer le bouton
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});
</script>
@endsection  