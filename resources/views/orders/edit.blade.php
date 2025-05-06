@extends('layouts.admin')
  
@section('title', 'Modifier la commande #' . $order->id)

@section('styles')
<!--<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />-->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">


<style>
    .status-history {
        max-height: 200px;
        overflow-y: auto;
    }
    .timeline-item {
        position: relative;
        padding-left: 30px;
        margin-bottom: 15px;
    }
    .timeline-item:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 2px;
        background-color: #e0e0e0;
    }
    .timeline-item:after {
        content: '';
        position: absolute;
        left: -4px;
        top: 0;
        height: 10px;
        width: 10px;
        border-radius: 50%;
        background-color: #007bff;
    }
    .order-images img {
        max-height: 150px;
        margin: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 3px;
    }
    .image-container {
        position: relative;
        display: inline-block;
    }
    .image-remove {
        position: absolute;
        top: 0;
        right: 0;
        background: rgba(255, 0, 0, 0.7);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        text-align: center;
        line-height: 20px;
        cursor: pointer;
    }

        
    .status-history {
        max-height: 200px;
        overflow-y: auto;
    }

    .timeline-item {
        position: relative;
        padding-left: 30px;
        margin-bottom: 15px;
    }

    .timeline-item:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 2px;
        background-color: #e0e0e0;
    }

    .timeline-item:after {
        content: '';
        position: absolute;
        left: -4px;
        top: 0;
        height: 10px;
        width: 10px;
        border-radius: 50%;
        background-color: #007bff;
    }

    .order-images img {
        max-height: 150px;
        margin: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 3px;
        cursor: pointer; /* Indiquer que l'image est cliquable */
        transition: transform 0.2s; /* Animation au survol */
    }

    .order-images img:hover {
        transform: scale(1.05); /* Légère augmentation de taille au survol */
        box-shadow: 0 0 5px rgba(0,0,0,0.2);
    }

    .image-container {
        position: relative;
        display: inline-block;
    }

    .image-remove {
        position: absolute;
        top: 0;
        right: 0;
        background: rgba(255, 0, 0, 0.7);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        text-align: center;
        line-height: 20px;
        cursor: pointer;
        z-index: 10;
    }

    /* Pour indiquer que les card-header sont cliquables */
    .card-header {
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .card-header:hover {
        background-color: #f8f9fa;
    }

    /* Masquer le bloc de sélection du client */
    #client_search_container {
        display: none;
    }
    .float-end{
        position: absolute;
        top:10px;
        right:10px;

    }
</style>
@endsection

@section('content')
<div class="container-">
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Modifier la commande #{{ $order->id }}</h2>
                <div>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Retour à la liste</a>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="row">
        <!-- Informations sur la commande -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Détails de la commande</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.update', $order->id) }}" method="POST" id="orderForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Section Client -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Information Client</h6>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="client_id" id="client_id" value="{{ $order->client->id ?? '' }}">                               
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <label for="phone">Téléphone *</label>
                                        <select name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" required>
                                            @if(old('phone', $order->client->phone ?? ''))
                                                <option value="{{ old('phone', $order->client->phone ?? '') }}" selected>
                                                    {{ old('phone', $order->client->phone ?? '') }}
                                                </option>
                                            @endif
                                        </select>
                                        @error('phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <!-- Champ hidden pour conserver la compatibilité avec la validation -->
                                        <input type="hidden" name="phone_hidden" id="phone_hidden">
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <label for="phone2">Téléphone 2</label>
                                        <input type="text" name="phone2" id="phone2" class="form-control @error('phone2') is-invalid @enderror"
                                            value="{{ old('phone2', $order->client->phone2 ?? '') }}">
                                        @error('phone2')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
 
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <label for="first_name">Prénom *</label>
                                        <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" required
                                            value="{{ old('first_name', $order->client->first_name ?? '') }}">
                                        @error('first_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <label for="last_name">Nom *</label>
                                        <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" required
                                            value="{{ old('last_name', $order->client->last_name ?? '') }}">
                                        @error('last_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <label for="city">Ville *</label>
                                        <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" required
                                            value="{{ old('city', $order->client->city ?? '') }}">
                                        @error('city')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <label for="delegation">Délégation *</label>
                                        <select name="delegation" id="delegation" class="form-control @error('delegation') is-invalid @enderror" required>
                                            <option value="">Sélectionner...</option>
                                            @foreach($delegations as $delegation)
                                                <option value="{{ $delegation }}" {{ old('delegation', $order->client->delegation ?? '') == $delegation ? 'selected' : '' }}>
                                                    {{ $delegation }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('delegation')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 col-sm-6 mb-3">
                                        <label for="address">Adresse *</label>
                                        <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" required
                                            value="{{ old('address', $order->client->address ?? '') }}">
                                        @error('address')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-2 col-sm-6 mb-3">
                                        <label for="postal_code">Code postal</label>
                                        <input type="text" name="postal_code" id="postal_code" class="form-control @error('postal_code') is-invalid @enderror"
                                            value="{{ old('postal_code', $order->client->postal_code ?? '') }}">
                                        @error('postal_code')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Section Livraison et Statut -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Livraison et Statut</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <label for="service_type">Service *</label>
                                        <select name="service_type" id="service_type" class="form-control @error('service_type') is-invalid @enderror" required>
                                            <option value="">Sélectionner...</option>
                                            @foreach($serviceTypes as $value => $label)
                                                <option value="{{ $value }}" {{ old('service_type', $order->service_type) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('service_type')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <label for="delivery_company_id">Société de livraison</label>
                                        <select name="delivery_company_id" id="delivery_company_id" class="form-control @error('delivery_company_id') is-invalid @enderror">
                                            <option value="">Sélectionner...</option>
                                            @foreach($deliveryCompanies as $company)
                                                <option value="{{ $company->id }}" 
                                                    data-price="{{ $company->delivery_price }}"
                                                    {{ old('delivery_company_id', $order->delivery_company_id) == $company->id ? 'selected' : '' }}>
                                                    {{ $company->name }} ({{ $company->delivery_price }} TND)
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('delivery_company_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <label for="status">Statut *</label>
                                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                            @foreach($statusOptions as $value => $label)
                                                <option value="{{ $value }}" {{ old('status', $order->status) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="form-check mt-2 float-right">
                                            <input type="checkbox" name="free_delivery" id="free_delivery" class="form-check-input" value="1"
                                                {{ old('free_delivery', $order->free_delivery) ? 'checked' : '' }}>
                                            <label for="free_delivery" class="form-check-label">Livraison gratuite</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="status_comment">Commentaire sur le changement de statut</label>
                                        <textarea name="status_comment" id="status_comment" class="form-control @error('status_comment') is-invalid @enderror" rows="2">{{ old('status_comment') }}</textarea>
                                        @error('status_comment')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                                                
                        <!-- Section Produits -->
                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Produits de la commande</h6>
                                <button type="button" class="btn btn-success btn-sm" id="add-product-btn" syle="cursor:cell!important">
                                    <i class="fas fa-plus"></i> Ajouter un produit
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="products-container">
                                    <!-- Les produits seront ajoutés ici dynamiquement -->
                                    @if(count($order->items) > 0)
                                        @foreach($order->items as $index => $item)
                                        <div class="product-item mb-3">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <strong>Produit:</strong>
                                                        <select name="items[{{ $index }}][product_id]" class="form-control product-select" required>
                                                            <option value="">Sélectionner un produit</option>
                                                            @foreach($products as $product)
                                                                <option value="{{ $product->id }}" 
                                                                        data-type="{{ $product->type }}" 
                                                                        data-price="{{ $product->prix_ttc }}"
                                                                        data-stock="{{ $product->stock_quantity }}"
                                                                        data-has-variations="{{ $product->variations->count() > 0 ? 'true' : 'false' }}"
                                                                        {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                                    {{ $product->name }} ({{ $product->reference }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group variation-container" style="{{ $item->variation_id ? '' : 'display: none;' }}">
                                                        <strong>Variation:</strong>
                                                        <select name="items[{{ $index }}][variation_id]" class="form-control variation-select">
                                                            <option value="">Sélectionner une variation</option>
                                                            @if($item->variation_id)
                                                                @foreach($products->find($item->product_id)->variations as $variation)
                                                                    <option value="{{ $variation->id }}" 
                                                                            data-price="{{ $variation->prix_ttc }}"
                                                                            data-stock="{{ $variation->stock_quantity }}"
                                                                            {{ $item->variation_id == $variation->id ? 'selected' : '' }}>
                                                                        {{ $variation->reference }}
                                                                        @if($variation->attributeValues)
                                                                            ({{ $variation->attributeValues->pluck('value')->join(' - ') }})
                                                                        @endif
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <strong>Qté:</strong>
                                                        <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-quantity" 
                                                            min="1" value="{{ $item->quantity }}" required>
                                                        <small class="text-muted stock-info">Stock: <span class="available-stock">{{ $item->variation_id ? $item->variation->stock_quantity : $item->product->stock_quantity }}</span></small>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <strong>Prix unitaire:</strong>
                                                        <input type="number" name="items[{{ $index }}][unit_price]" class="form-control item-price" 
                                                            step="0.01" min="0" value="{{ $item->unit_price }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <strong>Sous-total:</strong>
                                                        <input type="text" class="form-control item-subtotal" value="{{ number_format($item->unit_price * $item->quantity, 2) }} TND" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 pt-2">
                                                    <button type="button" class="btn btn-danger btn-sm remove-product-btn float-right mt-4">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="discount">Remise (TND):</label>
                                            <input type="number" name="discount" id="discount" class="form-control" step="0.01" min="0" 
                                                value="{{ old('discount', $order->discount ?? 0) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <span>Sous-total:</span>
                                                    <span id="subtotal-amount">0.00 TND</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Remise:</span>
                                                    <span id="discount-amount">0.00 TND</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Livraison:</span>
                                                    <span id="delivery-amount">0.00 TND</span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between">
                                                    <strong>Total:</strong>
                                                    <strong id="total-amount">0.00 TND</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">Annuler</a>
                            
                            <button type="button" class="btn btn-danger float-end" 
                                onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette commande?')) { 
                                    document.getElementById('delete-form').submit(); 
                                }">
                                Supprimer
                            </button>
                        </div>
                    </form>
                    
                    <form id="delete-form" action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>


            <!-- Template pour un élément de produit -->
            <template id="product-item-template">
                <div class="product-item mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Produit:</strong>
                                <select name="items[INDEX][product_id]" class="form-control product-select" required>
                                    <option value="">Sélectionner un produit</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                                data-type="{{ $product->type }}" 
                                                data-price="{{ $product->prix_ttc }}"
                                                data-stock="{{ $product->stock_quantity }}"
                                                data-has-variations="{{ $product->variations->count() > 0 ? 'true' : 'false' }}">
                                            {{ $product->name }} ({{ $product->reference }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group variation-container" style="display: none;">
                                <strong>Variation:</strong>
                                <select name="items[INDEX][variation_id]" class="form-control variation-select">
                                    <option value="">Sélectionner une variation</option>
                                    <!-- Les variations seront chargées dynamiquement -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <strong>Qté:</strong>
                                <input type="number" name="items[INDEX][quantity]" class="form-control item-quantity" min="1" value="1" required>
                                <small class="text-muted stock-info">Stock: <span class="available-stock">0</span></small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <strong>Prix unitaire:</strong>
                                <input type="number" name="items[INDEX][unit_price]" class="form-control item-price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <strong>Sous-total:</strong>
                                <input type="text" class="form-control item-subtotal" readonly>
                            </div>
                        </div>
                        <div class="col-md-1 pt-2">
                            <button type="button" class="btn btn-danger btn-sm remove-product-btn float-right mt-4">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Sidebar -->
        <div class="col-md-4">
            @if($order->images->count() > 0)
            <!-- Images -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 mb-0>Images</h6>
                </div>
                <div class="card-body"  @if($order->images->count()== 0) style="display: none;" @endif>
                    <div class="order-images">
                        @if($order->images->count() > 0)
                            @foreach($order->images as $image)
                                <div class="image-container">
                                    <img src="{{ asset($image->path) }}" alt="Image de commande" class="img-thumbnail">
                                    <span class="image-remove" data-id="{{ $image->id }}" title="Supprimer">×</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Aucune image pour cette commande</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            <!-- Section Notes -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Notes</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                    </div>
                </div>
            </div>
            
            <!-- Historique des statuts -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Historique des statuts</h6>
                </div>
                <div class="card-body">
                    <div class="status-history">
                        @if($order->statusHistory->count() > 0)
                            @foreach($order->statusHistory->sortByDesc('created_at') as $history)
                                <div class="timeline-item">
                                    <div class="small text-muted">{{ $history->created_at->format('d/m/Y H:i') }}</div>
                                    <div class="fw-bold">
                                        @if($history->old_status)
                                            {{ $statusOptions[$history->old_status] ?? $history->old_status }} → 
                                        @endif
                                        {{ $statusOptions[$history->new_status] ?? $history->new_status }}
                                    </div>
                                    @if($history->comment)
                                        <div>{{ $history->comment }}</div>
                                    @endif
                                    <div class="small">par {{ $history->user->name ?? 'Système' }}</div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Aucun historique disponible</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('footer-scripts')
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}" ></script>

<script>
  
  $(function () {
    // Summernote
    $('#notes').summernote();
  });
  
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
     
// Stockage des produits
const products = @json($products);

// Compteur pour les indices d'éléments
let itemIndex = {{ count($order->items ?? []) }};

$(document).ready(function() {
 
    var currentPhone = '{{ old("phone", $order->client->phone ?? "") }}';
    $('#phone').replaceWith('<input type="text" name="phone" id="phone" class="form-control @error("phone") is-invalid @enderror" required value="' + currentPhone + '">');
    
    // Ajouter jQuery UI autocomplete au champ téléphone
    $('#phone').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '{{ route("clients.search") }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    q: request.term,
                    type: 'phone'
                },
                success: function(data) {
                    // Transformer le format des résultats pour autocomplete
                    var results = $.map(data.results, function(item) {
                        return {
                            label: item.text,
                            value: item.text.split(' - ')[0], // Extraire juste le numéro de téléphone
                            client_id: item.id,
                            client_details: item.client_details
                        };
                    });
                    response(results);
                }
            });
        },
        minLength: 3,
        select: function(event, ui) {
            // Quand un client est sélectionné, remplir les autres champs
            if (ui.item && ui.item.client_details) {
                $('#client_id').val(ui.item.client_id);
                $('#phone2').val(ui.item.client_details.phone2);
                $('#first_name').val(ui.item.client_details.first_name);
                $('#last_name').val(ui.item.client_details.last_name);
                $('#city').val(ui.item.client_details.city);
                $('#delegation').val(ui.item.client_details.delegation);
                $('#address').val(ui.item.client_details.address);
                $('#postal_code').val(ui.item.client_details.postal_code);
            }
        }
    }).autocomplete("instance")._renderItem = function(ul, item) {
        // Personnaliser l'apparence des suggestions
        return $("<li>")
            .append("<div>" + item.label + "</div>")
            .appendTo(ul);
    };
    
    // Recherche par téléphone quand on quitte le champ
    $('#phone').on('blur', function() {
        var phone = $(this).val();
        if (phone.length >= 8 && !$('#client_id').val()) {
            $.ajax({
                url: '{{ route("clients.check-phone") }}',
                type: 'GET',
                data: { phone: phone },
                success: function(response) {
                    if (response.exists) {
                        // Remplir tous les champs avec les informations du client
                        $('#client_id').val(response.client.id);
                        $('#phone2').val(response.client.phone2);
                        $('#first_name').val(response.client.first_name);
                        $('#last_name').val(response.client.last_name);
                        $('#city').val(response.client.city);
                        $('#delegation').val(response.client.delegation);
                        $('#address').val(response.client.address);
                        $('#postal_code').val(response.client.postal_code);
                    }
                }
            });
        }
    });
    // 3. Cliquer sur une image pour l'afficher en grand dans une modal
    // Ajouter une modal au document
    $('body').append(`
        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Image de commande</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="" id="modalImage" class="img-fluid" alt="Image agrandie">
                    </div>
                </div>
            </div>
        </div>
    `);
    
    // Rendre les images cliquables
    $('.order-images img').css('cursor', 'pointer').on('click', function() {
        var imgSrc = $(this).attr('src');
        $('#modalImage').attr('src', imgSrc);
        var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        imageModal.show();
    });
    
    // 4. Rendre les card-header cliquables pour ouvrir/fermer card-body
    $('.card-header').css('cursor', 'pointer').on('click', function() {
        $(this).next('.card-body').slideToggle();
    });
    
    // Option: Ajouter une petite icône pour indiquer que c'est cliquable
    $('.card-header').append('<i class="fas fa-chevron-down float-end"></i>');
    $('.card-header').on('click', function() {
        $(this).find('.fa-chevron-down, .fa-chevron-up').toggleClass('fa-chevron-down fa-chevron-up');
    });

    $('.image-remove').on('click', function() {
        var imageId = $(this).data('id');
        var container = $(this).parent();
            
            if (confirm('Êtes-vous sûr de vouloir supprimer cette image?')) {
                $.ajax({
                    url: '{{ url("orders/delete-image") }}/' + imageId,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            container.fadeOut(300, function() { $(this).remove(); });
                        }
                    },
                    error: function() {
                        alert('Erreur lors de la suppression de l\'image');
                    }
                });
            }
    });


// Si aucun produit n'est présent, en ajouter un par défaut
if ($('.product-item').length === 0) {
        addProductItem();
    }
    
    // Gestionnaire pour le bouton "Ajouter un produit"
    $('#add-product-btn').click(function() {
        addProductItem();
    });
    
    // Gestionnaire pour les boutons de suppression d'un produit (délégation d'événement)
    $('#products-container').on('click', '.remove-product-btn', function() {
        $(this).closest('.product-item').remove();
        updateTotals();
    });
    
    // Gestionnaire pour la sélection de produit (délégation d'événement)
    $('#products-container').on('change', '.product-select', function() {
        const productId = $(this).val();
        const itemContainer = $(this).closest('.product-item');
        const variationContainer = itemContainer.find('.variation-container');
        const variationSelect = itemContainer.find('.variation-select');
        const priceInput = itemContainer.find('.item-price');
        const quantityInput = itemContainer.find('.item-quantity');
        const stockInfo = itemContainer.find('.available-stock');
        
        if (!productId) {
            // Réinitialiser les champs si aucun produit n'est sélectionné
            variationContainer.hide();
            variationSelect.empty().append('<option value="">Sélectionner une variation</option>');
            priceInput.val('');
            stockInfo.text('0');
            updateItemSubtotal(itemContainer);
            return;
        }
        
        const productOption = $(this).find('option:selected');
        const productType = productOption.data('type');
        const productPrice = productOption.data('price');
        const productStock = productOption.data('stock');
        const hasVariations = productOption.data('has-variations') === true;
        
        // Mettre à jour le prix
        priceInput.val(productPrice || '');
        
        // Mettre à jour l'info de stock et limiter la quantité max
        stockInfo.text(productStock);
        quantityInput.attr('max', productStock);
        
        // Gérer les variations si c'est un produit variable
        if (productType == 1 && hasVariations) {
            loadVariations(productId, variationSelect);
            variationContainer.show();
        } else {
            variationContainer.hide();
            variationSelect.empty().append('<option value="">Sélectionner une variation</option>');
        }
        
        updateItemSubtotal(itemContainer);
    });
    
    // Gestionnaire pour la sélection de variation
    $('#products-container').on('change', '.variation-select', function() {
        const variationId = $(this).val();
        const itemContainer = $(this).closest('.product-item');
        const priceInput = itemContainer.find('.item-price');
        const quantityInput = itemContainer.find('.item-quantity');
        const stockInfo = itemContainer.find('.available-stock');
        
        if (variationId) {
            // Trouver le produit et la variation
            const productId = itemContainer.find('.product-select').val();
            const product = products.find(p => p.id == productId);
            const variation = product.variations.find(v => v.id == variationId);
            
            if (variation) {
                // Mettre à jour le prix et les informations de stock
                priceInput.val(variation.prix_ttc || product.prix_ttc || '');
                stockInfo.text(variation.stock_quantity);
                quantityInput.attr('max', variation.stock_quantity);
                
                // Si la quantité actuelle dépasse le stock disponible, ajuster
                if (parseInt(quantityInput.val()) > variation.stock_quantity) {
                    quantityInput.val(variation.stock_quantity);
                }
            }
        }
        
        updateItemSubtotal(itemContainer);
    });
    
    // Gestionnaire pour les changements de quantité et de prix
    $('#products-container').on('input', '.item-quantity, .item-price', function() {
        const itemContainer = $(this).closest('.product-item');
        
        // Vérifier que la quantité ne dépasse pas le stock disponible
        if ($(this).hasClass('item-quantity')) {
            const maxStock = parseInt(itemContainer.find('.available-stock').text());
            const currentQty = parseInt($(this).val());
            
            if (currentQty > maxStock) {
                alert(`Attention: Stock disponible insuffisant (${maxStock} unités disponibles)`);
                $(this).val(maxStock);
            }
        }
        
        updateItemSubtotal(itemContainer);
    });
    
    // Écouter les changements sur la remise et la livraison
    $('#discount, #delivery_company_id, #free_delivery').on('change input', function() {
        updateTotals();
    });
    
    // Initialiser les totaux au chargement
    updateTotals();



}); //document ready

  // Fonction pour ajouter un élément de produit
function addProductItem() {
    const template = document.getElementById('product-item-template').innerHTML;
    const newItem = template.replace(/INDEX/g, itemIndex);
    
    $('#products-container').append(newItem);
    itemIndex++;
}

// Fonction pour charger les variations d'un produit
function loadVariations(productId, selectElement) {
    const product = products.find(p => p.id == productId);
    
    if (!product || !product.variations || product.variations.length === 0) {
        selectElement.empty().append('<option value="">Aucune variation disponible</option>');
        return;
    }
    
    selectElement.empty().append('<option value="">Sélectionner une variation</option>');
    
    product.variations.forEach(variation => {
        // Créer un nom lisible pour la variation basé sur ses attributs
        let variationName = variation.reference;
        
        if (variation.attribute_values && variation.attribute_values.length > 0) {
            const attributeValues = variation.attribute_values.map(av => av.value || av.name).join(' - ');
            if (attributeValues) {
                variationName += ' (' + attributeValues + ')';
            }
        }
        
        selectElement.append(`
            <option value="${variation.id}" 
                    data-price="${variation.prix_ttc || product.prix_ttc || ''}"
                    data-stock="${variation.stock_quantity}">
                ${variationName}
            </option>
        `);
    });
}

// Fonction pour mettre à jour le sous-total d'un élément
function updateItemSubtotal(itemContainer) {
    const quantity = parseFloat(itemContainer.find('.item-quantity').val()) || 0;
    const price = parseFloat(itemContainer.find('.item-price').val()) || 0;
    const subtotal = quantity * price;
    
    itemContainer.find('.item-subtotal').val(subtotal.toFixed(2) + ' TND');
    
    updateTotals();
}

// Fonction pour mettre à jour tous les totaux de la commande
function updateTotals() {
    let subtotal = 0;
    
    // Calculer le sous-total des produits
    $('.product-item').each(function() {
        const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
        const price = parseFloat($(this).find('.item-price').val()) || 0;
        subtotal += quantity * price;
    });
    
    // Récupérer la remise
    const discount = parseFloat($('#discount').val()) || 0;
    
    // Calculer les frais de livraison
    let deliveryCost = 0;
    if (!$('#free_delivery').is(':checked')) {
        const deliveryCompanySelect = $('#delivery_company_id');
        if (deliveryCompanySelect.val()) {
            const selectedOption = deliveryCompanySelect.find('option:selected');
            deliveryCost = parseFloat(selectedOption.data('price')) || 0;
        }
    }
    
    // Calculer le total final
    const total = subtotal - discount + deliveryCost;
    
    // Mettre à jour les affichages
    $('#subtotal-amount').text(subtotal.toFixed(2) + ' TND');
    $('#discount-amount').text(discount.toFixed(2) + ' TND');
    $('#delivery-amount').text(deliveryCost.toFixed(2) + ' TND');
    $('#total-amount').text(total.toFixed(2) + ' TND');
    
    // Mettre à jour le champ caché pour le total si nécessaire
    if ($('#total').length) {
        $('#total').val(total.toFixed(2));
    }
}
        
</script>
@endsection