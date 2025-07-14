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
        /*max-height: 150px;*/
        /*max-width:400px;*/
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



    /* Masquer le bloc de sélection du client */
    #client_search_container {
        display: none;
    }

    #discount{
        max-width:150px;
    }
    .item-subtotal{
        font-size:10px;
        padding:2px 2px;
    }

    .btn-danger{
        position: absolute;
        top:6px;right:6px;
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

    /* Désactiver visuellement le champ discount */
    #discount {
        background-color: #f8f9fa;
        cursor: not-allowed;
        opacity: 0.8;
    }

    /* Améliorer l'apparence des boutons de code promo */
    .apply-promo-btn {
        transition: all 0.2s ease;
    }

    .apply-promo-btn:hover {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }

    .remove-promo-btn {
        transition: all 0.2s ease;
    }

    .remove-promo-btn:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    /* Style pour les produits gratuits */
    .free-product .item-price {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
        font-weight: bold;
    }

    .free-product .remove-product-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endsection

@section('content')
<div class="container-">
    <div class="row">
        <div class="col-md-12 mb-1">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Modifier la commande #{{ $order->id }}</h2>
                <div>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Retour à la liste</a>
                </div>
            </div>
        </div>
    </div>

   
    <div class="row">
        <!-- Informations sur la commande -->
        <div class="col-md-8">
            <div class="card mb-2">
                <div class="card-header">
                    <h5>Détails de la commande</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.update', $order->id) }}" method="POST" id="orderForm">
                        @csrf
                        @method('PUT')
                        
                        @if($order->parcel && $order->parcel->reference)
                            <div class="">
                                Colis #{{$order->parcel->id}}   : <a href="{{ route('parcels.show', $order->parcel->id) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-2">{{ $order->parcel->reference }}</a>
                            </div>
                        @endif

                        <!-- Section Client -->
                        <div class="card mb-2">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Information Client</h6>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="client_id" id="client_id" value="{{ $order->client->id ?? '' }}">                               
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 mb-1">
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
                                    <div class="col-md-3 col-sm-6 mb-1">
                                        <label for="phone2">Téléphone 2</label>
                                        <input type="text" name="phone2" id="phone2" class="form-control @error('phone2') is-invalid @enderror"
                                            value="{{ old('phone2', $order->client->phone2 ?? '') }}">
                                        @error('phone2')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
 
                                    <div class="col-md-3 col-sm-6 mb-1">
                                        <label for="first_name">Prénom *</label>
                                        <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" required
                                            value="{{ old('first_name', $order->client->first_name ?? '') }}">
                                        @error('first_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-1">
                                        <label for="last_name">Nom </label>
                                        <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror"  
                                            value="{{ old('last_name', $order->client->last_name ?? '') }}">
                                        @error('last_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 col-sm-6 mb-1">
                                        <label for="city">Ville *</label>
                                        <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" required
                                            value="{{ old('city', $order->client->city ?? '') }}">
                                        @error('city')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-1">
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

                                    <div class="col-md-4 col-sm-6 mb-1">
                                        <label for="address">Adresse *</label>
                                        <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" required
                                            value="{{ old('address', $order->client->address ?? '') }}">
                                        @error('address')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-2 col-sm-6 mb-1">
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
                        <div class="card mb-2">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Livraison et Statut</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 mb-1">
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
                                    <div class="col-md-3 col-sm-6 mb-1">
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

                                    <div class="col-md-3 col-sm-6 mb-1">
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

                                    <div class="col-md-3 col-sm-6 mb-1">
                                        <div class="form-check mt-2 float-right">
                                            <input type="checkbox" name="free_delivery" id="free_delivery" class="form-check-input" value="1"
                                                {{ old('free_delivery', $order->free_delivery) ? 'checked' : '' }}>
                                            <label for="free_delivery" class="form-check-label">Livraison gratuite</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-1">
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
                        <div class="card mb-2" data-order-id="{{ $order->id }}">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Produits de la commande</h6>
                                <button type="button" class="btn btn-success btn-sm" id="add-product-btn">
                                    <i class="fas fa-plus"></i> Ajouter un produit
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="products-container">
                                    <!-- Les produits seront ajoutés ici dynamiquement -->
                                    @if(count($order->items) > 0)
                                        @foreach($order->items as $index => $item)
                                        <div class="product-item">
                                            <div class="row">
                                                <div class="col-lg-3 col-md-6">
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
                                                <div class="col-lg-3 col-md-6">
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
                                                <div class="col-lg-2 col-md-3">
                                                    <div class="form-group">
                                                        <strong>Qté:</strong>
                                                        <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-quantity" 
                                                            min="1" value="{{ $item->quantity }}" required>
                                                        <small class="text-muted stock-info">Stock: <span class="available-stock">{{ $item->variation_id ? $item->variation->stock_quantity : $item->product->stock_quantity }}</span></small>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-md-3">
                                                    <div class="form-group">
                                                        <strong>Prix U:</strong>
                                                        <input type="number" name="items[{{ $index }}][unit_price]" class="form-control item-price" 
                                                            step="0.01" min="0" value="{{ $item->unit_price }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-md-3">
                                                    <div class="form-group">
                                                        <strong>Tot:</strong>
                                                        <input type="text" class="form-control item-subtotal" value="{{ number_format($item->unit_price * $item->quantity, 2) }} TND" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1 col-md-3 pt-2">
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
                                                value="{{  $order->discount ?? 0  }}" title="Remise appliquée automatiquement par le code promo">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> La remise est calculée automatiquement selon le code promo appliqué
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <span>Sous-total:</span>
                                                    <span id="subtotal-amount">{{  $order->subtotal ?? 0  }} TND</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Remise:</span>
                                                    <span id="discount-amount">{{  $order->discount ?? 0  }} TND</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Livraison:</span>
                                                    <span id="delivery-amount">{{  $order->delivery_cost ?? 0  }} TND</span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between">
                                                    <strong>Total:</strong>
                                                    <strong id="total-amount">{{  $order->total ?? 0  }} TND</strong>
                                                </div>
                                                <input type="hidden"   id="total"  value="{{  $order->total ?? 0  }}"  />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Section Codes Promos -->
                        <div class="card mb-2" style="display:none">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Codes Promos</h6>
                                <button type="button" class="btn btn-success btn-sm"  id="add-promo-btn"  >
                                    <i class="fas fa-plus"></i> Créer
                                </button>
                            </div>
                            <div class="card-body">
                                @if($order->client && $order->client->promoCodes->count() > 0)
                                    <div class="promo-codes-list">
                                        @foreach($order->client->promoCodes as $promo)
                                            <div class="promo-item mb-2 p-3 border rounded {{ $promo->id == $order->promo_code_id ? 'bg-success- bg-opacity-10 border-success' : 'bg-light' }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-1">
                                                            <strong class="me-2">{{ $promo->code }}</strong>
                                                            @if($promo->id == $order->promo_code_id)
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-check"></i> Appliqué
                                                                </span>
                                                            @endif
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
                                                            @elseif($promo->is_used && $promo->id != $order->promo_code_id)
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
                                                    
                                                    <div class="ms-3">
                                                        @if($promo->id == $order->promo_code_id)
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-promo-btn" 
                                                                    data-promo-id="{{ $promo->id }}"
                                                                    title="Retirer ce code promo">
                                                                <i class="fas fa-times"></i> Retirer
                                                            </button>
                                                        @elseif(!$promo->is_used && (!$promo->expires_at || !$promo->expires_at->isPast()))
                                                            <button type="button" class="btn btn-sm btn-outline-success apply-promo-btn" 
                                                                    data-promo-id="{{ $promo->id }}"
                                                                    data-promo-type="{{ $promo->type }}"
                                                                    data-promo-value="{{ $promo->value }}"
                                                                    data-promo-product-id="{{ $promo->product_id }}"
                                                                    title="Appliquer ce code promo">
                                                                <i class="fas fa-check"></i> Appliquer
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                                <i class="fas fa-ban"></i> Indisponible
                                                            </button>
                                                        @endif
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
                                
                                <!-- Champ hidden pour le code promo sélectionné -->
                                <input type="hidden" name="promo_code_id" id="promo_code_id" value="{{ $order->promo_code_id ?? '' }}">
                                <input type="hidden" name="promo_code_type" id="promo_code_type" value="{{ $order->promoCode->type ?? '' }}">
                                <input type="hidden" name="promo_code_value" id="promo_code_value" value="{{ $order->promoCode->value ?? '' }}">
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
                    @if($order->delivery_company_id > 1 &&  isset($order->items) )
                    <form method="POST" action="{{ route('parcels.store', $order->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success float-right" >Créer et envoyer colis</button>
                    </form>
                    @endif
                    <form id="delete-form" action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>


            <!-- Template pour un élément de produit -->
            <template id="product-item-template">
                <div class="product-item">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
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
                        <div class="col-lg-3 col-sm-6">
                            <div class="form-group variation-container" style="display: none;">
                                <strong>Variation:</strong>
                                <select name="items[INDEX][variation_id]" class="form-control variation-select">
                                    <option value="">Sélectionner une variation</option>
                                    <!-- Les variations seront chargées dynamiquement -->
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-3">
                            <div class="form-group">
                                <strong>Qté:</strong>
                                <input type="number" name="items[INDEX][quantity]" class="form-control item-quantity" min="1" value="1" required>
                                <small class="text-muted stock-info">Stock: <span class="available-stock">0</span></small>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-3">
                            <div class="form-group">
                                <strong>Prix U:</strong>
                                <input type="number" name="items[INDEX][unit_price]" class="form-control item-price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-3">
                            <div class="form-group">
                                <strong>Tot:</strong>
                                <input type="text" class="form-control item-subtotal" readonly>
                            </div>
                        </div>
                        <div class="col-lg-1 col-sm-3 pt-2">
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
            <div class="card mb-2">
                <div class="card-header bg-light">
                    <h6 mb-0>Images</h6>
                </div>
                <div class="card-body"  @if($order->images->count()== 0 || $order->parcel) style="display: none;" @endif>
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
                                    <div class="small">par {{ $history->user->name ?? 'API' }}</div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Aucun historique disponible</p>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Section Notes -->
            <div class="card mb-2">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Notes</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid  @enderror"  data-order-id="{{ $order->id }}" rows="3">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                    </div>
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
                    <input type="hidden" name="client_id" value="{{ $order->client->id ?? '' }}">
                                            
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
 
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
     
// Stockage des produits
const products = @json($products);

// Compteur pour les indices d'éléments
let itemIndex = {{ count($order->items ?? []) }};

// Stockage des données du code promo actuellement appliqué
let currentPromoData = null;

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

    $('#add-promo-btn').click(function() {
        addPromo();
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
        ///quantityInput.attr('max', productStock);
        
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
                ///quantityInput.attr('max', variation.stock_quantity);
                
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
        /*
        // Vérifier que la quantité ne dépasse pas le stock disponible
        if ($(this).hasClass('item-quantity')) {
            const maxStock = parseInt(itemContainer.find('.available-stock').text());
            const currentQty = parseInt($(this).val());
            
            if (currentQty > maxStock) {
                alert(`Attention: Stock disponible insuffisant (${maxStock} unités disponibles)`);
                $(this).val(maxStock);
            }
        }
        */
        updateItemSubtotal(itemContainer);
    });
    
    // Écouter les changements sur la remise et la livraison
    $('#discount, #delivery_company_id, #free_delivery').on('change input', function() {
        updateTotals();
    });
    
    // Initialiser les totaux au chargement
    updateTotals();

    $('#notes').on('change', function() {
        const orderId = $(this).data('order-id');
        const notes = $(this).val();
        const feedback = $('#notes-feedback');
        const textarea = $(this);

        // Visual feedback pendant la sauvegarde
        textarea.addClass('border-warning');
        feedback.html('<small class="text-warning"><i class="fas fa-spinner fa-spin"></i> Sauvegarde en cours...</small>');

        $.ajax({
            url: `/orders/update-notes`,
            method: 'post',
            headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                notes: notes,
                order: orderId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                textarea.removeClass('border-warning').addClass('border-success');
                feedback.html('<small class="text-success"><i class="fas fa-check"></i> Notes sauvegardées</small>');
                
                // Effacer le message après 3 secondes
                setTimeout(function() {
                    textarea.removeClass('border-success');
                    feedback.html('');
                }, 3000);
            },
            error: function(xhr) {
                textarea.removeClass('border-warning').addClass('border-danger');
                
                let errorMessage = 'Erreur lors de la sauvegarde';
                if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.notes) {
                    errorMessage = xhr.responseJSON.errors.notes[0];
                }
                
                feedback.html(`<small class="text-danger"><i class="fas fa-exclamation-triangle"></i> ${errorMessage}</small>`);
                
                // Effacer le message d'erreur après 5 secondes
                setTimeout(function() {
                    textarea.removeClass('border-danger');
                    feedback.html('');
                }, 5000);
            }
        });
    });



    // Désactiver le champ discount pour empêcher la modification manuelle
    //$('#discount').prop('readonly', true);
        
    // Initialiser les données du code promo si un code est déjà appliqué
    initializeCurrentPromo();
    
    // Gestionnaire pour appliquer un code promo (délégation d'événement)
    $(document).on('click', '.apply-promo-btn', function() {
        const promoId = $(this).data('promo-id');
        const promoType = $(this).data('promo-type');
        const promoValue = $(this).data('promo-value');
        const promoProductId = $(this).data('promo-product-id');
        
        // Stocker les données du code promo
        currentPromoData = {
            id: promoId,
            type: promoType,
            value: promoValue,
            product_id: promoProductId
        };
        
        // Mettre à jour les champs hidden
        $('#promo_code_id').val(promoId);
        $('#promo_code_type').val(promoType);
        $('#promo_code_value').val(promoValue);
        
        // Appliquer le code promo selon son type
        applyPromoCode(currentPromoData);
        
        // Recalculer les totaux
        updateTotals();
        
        // CORRIGER: Appeler usePromoCode APRÈS que les totaux soient calculés
        setTimeout(() => {
            usePromoCode();
        }, 100);
    });
    
    // Gestionnaire pour retirer un code promo
    $(document).on('click', '.remove-promo-btn', function() {
        // Supprimer les produits gratuits s'il y en a
        if (currentPromoData && currentPromoData.type === 'free_product') {
            $('.free-product').remove();
        }
        
        // Réinitialiser les données
        currentPromoData = null;
        $('#promo_code_id').val('');
        $('#discount').val(0);
        
        // Recalculer les totaux
        updateTotals();
        
        // Recharger la page pour mettre à jour l'affichage
        location.reload();
    });
    
    // Écouter les changements sur les champs qui affectent le calcul
    $('#delivery_company_id, #free_delivery').on('change', function() {
        updateTotals();
    });
    
    // Écouter les changements sur les produits pour recalculer avec le code promo
    $('#products-container').on('input change', '.item-quantity, .item-price, .product-select, .variation-select', function() {
        const itemContainer = $(this).closest('.product-item');
        updateItemSubtotal(itemContainer);
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


/* Codes promos */

function calculateTotals() {
    let subtotal = 0;
    
    // Calculer le sous-total des produits
    document.querySelectorAll('.product-item').forEach(function(item) {
        const quantity = parseFloat(item.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(item.querySelector('.item-price').value) || 0;
        const itemTotal = quantity * price;
        
        item.querySelector('.item-subtotal').value = itemTotal.toFixed(2) + ' TND';
        subtotal += itemTotal;
    });
    
    // Remise manuelle
    const manualDiscount = parseFloat(document.getElementById('discount').value) || 0;
    
    // Remise du code promo
    let promoDiscount = 0;
    const promoCodeId = document.getElementById('promo_code_id').value;
    
    if (promoCodeId) {
        const promoData = getPromoCodeData(promoCodeId);
        if (promoData) {
            if (promoData.type === 'percentage') {
                promoDiscount = (subtotal * promoData.value) / 100;
            } else if (promoData.type === 'fixed_amount') {
                promoDiscount = promoData.value;
            }
            // Pour free_product, le produit est ajouté avec prix = 0
        }
    }
    
    // Total des remises
    const totalDiscount = manualDiscount + promoDiscount;
    
    // Frais de livraison
    let deliveryFee = 0;
    const freeDelivery = document.getElementById('free_delivery').checked;
    if (!freeDelivery) {
        const deliveryCompany = document.getElementById('delivery_company_id');
        if (deliveryCompany.value) {
            const selectedOption = deliveryCompany.options[deliveryCompany.selectedIndex];
            deliveryFee = parseFloat(selectedOption.dataset.price) || 0;
        }
    }
    
    // Total final
    const total = Math.max(0, subtotal - totalDiscount + deliveryFee);
    
    // Mise à jour de l'affichage
    document.getElementById('subtotal-amount').textContent = subtotal.toFixed(2) + ' TND';
    document.getElementById('discount-amount').textContent = totalDiscount.toFixed(2) + ' TND';
    document.getElementById('delivery-amount').textContent = deliveryFee.toFixed(2) + ' TND';
    document.getElementById('total-amount').textContent = total.toFixed(2) + ' TND';

    document.getElementById('total').value = total.toFixed(2);
    document.getElementById('discount').value = totalDiscount.toFixed(2);

    
    // Afficher le détail des remises si code promo appliqué
    updateDiscountDetails(manualDiscount, promoDiscount);
}

function getPromoCodeData(promoCodeId) {
    // Récupérer les données du code promo depuis l'élément DOM
    const promoButton = document.querySelector(`[data-promo-id="${promoCodeId}"]`);
    if (promoButton) {
        return {
            type: promoButton.dataset.promoType,
            value: parseFloat(promoButton.dataset.promoValue),
            product_id: promoButton.dataset.promoProductId
        };
    }
    
    // AJOUTER: Fallback vers les champs hidden si bouton pas trouvé
    const typeField = document.getElementById('promo_code_type');
    const valueField = document.getElementById('promo_code_value');
    
    if (typeField && typeField.value) {
        return {
            type: typeField.value,
            value: parseFloat(valueField.value || 0),
            product_id: null // À adapter selon vos besoins
        };
    }
    
    return null;
}

function updateDiscountDetails(manualDiscount, promoDiscount) {
    const discountElement = document.getElementById('discount-amount');
    
    if (manualDiscount > 0 && promoDiscount > 0) {
        discountElement.title = `Remise manuelle: ${manualDiscount.toFixed(2)} TND + Code promo: ${promoDiscount.toFixed(2)} TND`;
    } else if (promoDiscount > 0) {
        discountElement.title = `Code promo: ${promoDiscount.toFixed(2)} TND`;
    } else if (manualDiscount > 0) {
        discountElement.title = `Remise manuelle: ${manualDiscount.toFixed(2)} TND`;
    } else {
        discountElement.title = '';
    }
}


// Gestion de la validation du formulaire avec codes promos
function usePromoCode() {
    const promoCodeId = document.getElementById('promo_code_id').value;
    const total = parseFloat($('#total').val()) || 0;
    const discount = parseFloat($('#discount').val()) || 0;
    
    // RETIRER cette ligne qui cause une alerte
    // alert(total + ' ' + discount);
    
    if (promoCodeId) {
        // Marquer le code promo comme utilisé
        fetch(`/promo-codes/${promoCodeId}/use`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                // CORRIGER: Récupérer l'ID de commande correctement
                order_id: getOrderId(), // Fonction à ajouter
                discount: discount,
                total: total,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Code promo utilisé avec succès');
            } else {
                console.error('Erreur:', data.message);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la mise à jour du code promo:', error);
        });
    }
}
function getOrderId() {
    // Récupérer l'ID de commande depuis l'URL ou un élément DOM
    const urlParts = window.location.pathname.split('/');
    const orderIndex = urlParts.indexOf('orders');
    if (orderIndex !== -1 && urlParts[orderIndex + 1]) {
        return urlParts[orderIndex + 1];
    }
    
    // Alternative: depuis un élément DOM
    const orderIdElement = document.querySelector('[data-order-id]');
    if (orderIdElement) {
        return orderIdElement.dataset.orderId;
    }
    
    return null;
}
// Écouter les changements sur les champs qui affectent le calcul
document.addEventListener('DOMContentLoaded', function() {
    // Recalculer quand la remise manuelle change
    document.getElementById('discount').addEventListener('input', calculateTotals);
    
    // Recalculer quand la livraison gratuite change
    document.getElementById('free_delivery').addEventListener('change', calculateTotals);
    
    // Recalculer quand la société de livraison change
    document.getElementById('delivery_company_id').addEventListener('change', calculateTotals);
    
    // Calcul initial
    //calculateTotals();
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
    /*
    document.getElementById('createPromoForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('{{ route("promo-codes.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                // Gestion des erreurs de validation Laravel
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
                //const modal = bootstrap.Modal.getInstance(document.getElementById('createPromoModal'));
                //modal.hide();
                $('#createPromoModal').hide();
                location.reload();
            } else {
                alert('Erreur lors de la création du code promo: ' + (data.message || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
    */
 
 
    
    
 
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

function debugPromoCalculation() {
    const promoCodeId = $('#promo_code_id').val();
    const promoData = getPromoCodeData(promoCodeId);
    
    console.log('Debug Promo:');
    console.log('- Promo ID:', promoCodeId);
    console.log('- Promo Data:', promoData);
    console.log('- Current Promo Data:', currentPromoData);
}

function applyPromoCode(promoData) {
    if (!promoData) return;
    
    switch (promoData.type) {
        case 'percentage':
            // La remise en pourcentage sera calculée dans updateTotals()
            break;
            
        case 'fixed_amount':
            // La remise fixe sera appliquée dans updateTotals()
            break;
            
        case 'free_product':
            // Ajouter le produit gratuit
            addFreeProduct(promoData.product_id);
            break;
    }

}

function addFreeProduct(productId) {
    // Éviter d'ajouter plusieurs fois le même produit gratuit
    if ($('.free-product').length > 0) {
        $('.free-product').remove();
    }
    
    // Utiliser le template pour ajouter un nouveau produit
    const template = $('#product-item-template').html();
    const newItem = template.replace(/INDEX/g, itemIndex);
    
    // Ajouter l'élément au conteneur
    $('#products-container').append(newItem);
    
    // Configurer le produit ajouté
    const newProductItem = $('#products-container .product-item').last();
    newProductItem.addClass('free-product');
    
    // Sélectionner le produit
    const productSelect = newProductItem.find('.product-select');
    productSelect.val(productId);
    
    // Définir le prix à 0 et le rendre readonly
    const priceInput = newProductItem.find('.item-price');
    priceInput.val('0.00');
    priceInput.prop('readonly', true);
    
    // Définir la quantité par défaut
    const quantityInput = newProductItem.find('.item-quantity');
    quantityInput.val(1);
    
    // Ajouter un indicateur visuel
    const indicator = $('<span class="badge bg-success ms-2">Gratuit (Code promo)</span>');
    productSelect.closest('.form-group').append(indicator);
    
    // Déclencher l'événement change pour charger les variations si nécessaire
    productSelect.trigger('change');
    
    // Incrémenter l'index
    itemIndex++;
    
    // Empêcher la suppression du produit gratuit
    const removeBtn = newProductItem.find('.remove-product-btn');
    removeBtn.prop('disabled', true);
    removeBtn.addClass('disabled');
    removeBtn.attr('title', 'Produit gratuit - Ne peut pas être supprimé');
}

// Fonction mise à jour pour calculer les totaux avec les codes promos
function updateTotals() {
    let subtotal = 0;
    
    // Calculer le sous-total des produits
    $('.product-item').each(function() {
        const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
        const price = parseFloat($(this).find('.item-price').val()) || 0;
        subtotal += quantity * price;
    });
    
    // Calculer la remise du code promo
    let promoDiscount = 0;
    const promoCodeId = $('#promo_code_id').val();

    if (promoCodeId && promoCodeId > 0) {
        const currentPromoData = getPromoCodeData(promoCodeId);
        
        if (currentPromoData) {
            switch (currentPromoData.type) {
                case 'percentage':
                    promoDiscount = (subtotal * currentPromoData.value) / 100;
                    break;
                case 'fixed_amount':
                    promoDiscount = parseFloat(currentPromoData.value);
                    break;
                case 'free_product':
                    // La remise est déjà prise en compte par le produit à prix 0
                    promoDiscount = 0;
                    break;
            }
        }

        console.log(currentPromoData.value, 'Code promo value de:', promoCodeId);
        console.log(currentPromoData.type, 'Code promo type de:', promoCodeId);
        console.log(promoDiscount, 'Remise appliquée pour le code promo:', promoCodeId); 
    }else{
        console.log('Aucun code promo appliqué ou ID invalide');
    }
    
    // Mettre à jour le champ discount avec la remise calculée
    $('#discount').val(promoDiscount.toFixed(2));
    
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
    const total = Math.max(0, subtotal - promoDiscount + deliveryCost);
    
    // Mettre à jour les affichages
    $('#subtotal-amount').text(subtotal.toFixed(2) + ' TND');
    $('#discount-amount').text(promoDiscount.toFixed(2) + ' TND');
    $('#delivery-amount').text(deliveryCost.toFixed(2) + ' TND');
    $('#total-amount').text(total.toFixed(2) + ' TND');
    
    // CORRIGER: Mettre à jour le champ hidden du total
    $('#total').val(total.toFixed(2));
    
    // Mettre à jour le titre du discount pour afficher le type de remise
    const currentPromoData = getPromoCodeData(promoCodeId);
    if (currentPromoData && promoDiscount > 0) {
        let discountTitle = '';
        switch (currentPromoData.type) {
            case 'percentage':
                discountTitle = `Code promo: -${currentPromoData.value}% (${promoDiscount.toFixed(2)} TND)`;
                break;
            case 'fixed_amount':
                discountTitle = `Code promo: -${promoDiscount.toFixed(2)} TND`;
                break;
            case 'free_product':
                discountTitle = 'Code promo: Produit gratuit';
                break;
        }
        $('#discount-amount').attr('title', discountTitle);
    } else {
        $('#discount-amount').removeAttr('title');
    }

    console.log('Totaux mis à jour - Subtotal:', subtotal, 'Promo:', promoDiscount, 'Total:', total);
}

// Fonction mise à jour pour le sous-total des éléments
function updateItemSubtotal(itemContainer) {
    const quantity = parseFloat(itemContainer.find('.item-quantity').val()) || 0;
    const price = parseFloat(itemContainer.find('.item-price').val()) || 0;
    const subtotal = quantity * price;
    
    itemContainer.find('.item-subtotal').val(subtotal.toFixed(2) + ' TND');
    
    // Recalculer les totaux avec le code promo
    updateTotals();
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