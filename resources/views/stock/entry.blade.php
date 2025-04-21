@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="float-left">
            <h2>Nouvelle entrée de stock</h2>
        </div>
        <div class="float-right">
            <a class="btn btn-primary" href="{{ route('stock.entries.index') }}"> Retour</a>
        </div>
    </div>
</div>

<form action="{{ route('stock.entries.store') }}" method="POST" id="stockEntryForm">
    @csrf

    <div class="card">
        <div class="card-header">
            <h4>Informations générales</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <strong>Date:</strong>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <strong>Référence:</strong>
                        <input type="text" name="reference" class="form-control" value="ES-{{ date('YmdHis') }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <strong>Description:</strong>
                        <textarea name="description" class="form-control" rows="1"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Produits</h4>
            <button type="button" class="btn btn-success" id="add-product-btn">
                <i class="fas fa-plus"></i> Ajouter un produit
            </button>
        </div>
        <div class="card-body">
            <div id="products-container">
                <!-- Les produits seront ajoutés ici dynamiquement -->
            </div>
            <div class="text-right mt-3">
                <strong>Total: <span id="total-amount">0.00</span> Dt</strong>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-lg-12 mt-3">
        <button type="submit" class="btn btn-primary">Enregistrer l'entrée de stock</button>
    </div>
</form>

<!-- Template pour un élément de produit -->
<template id="product-item-template">
    <div class="product-item card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Produit</h5>
            <button type="button" class="btn btn-danger btn-sm remove-product-btn">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <strong>Produit:</strong>
                        <select name="items[INDEX][product_id]" class="form-control product-select" required>
                            <option value="">Sélectionner un produit</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                        data-type="{{ $product->type }}" 
                                        data-price="{{ $product->prix_achat }}"
                                        data-has-variations="{{ $product->variations->count() > 0 ? 'true' : 'false' }}">
                                    {{ $product->name }} ({{ $product->reference }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6 variation-container" style="display: none;">
                    <div class="form-group">
                        <strong>Variation:</strong>
                        <select name="items[INDEX][variation_id]" class="form-control variation-select">
                            <option value="">Sélectionner une variation</option>
                            <!-- Les variations seront chargées dynamiquement -->
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <strong>Quantité:</strong>
                        <input type="number" name="items[INDEX][quantity]" class="form-control item-quantity" min="1" value="1" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <strong>Prix d'achat unitaire:</strong>
                        <input type="number" name="items[INDEX][prix_achat]" class="form-control item-price" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <strong>Sous-total:</strong>
                        <input type="text" class="form-control item-subtotal" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@section('footer-scripts')
<script>
    // Stockage des produits et variations
    const products = @json($products);
    
    // Compteur pour les indices d'éléments
    let itemIndex = 0;
    
    $(document).ready(function() {
        // Ajouter un premier produit au chargement
        addProductItem();
        
        // Gestionnaire pour le bouton "Ajouter un produit"
        $('#add-product-btn').click(function() {
            addProductItem();
        });
        
        // Gestionnaire pour les boutons de suppression d'un produit (délégation d'événement)
        $('#products-container').on('click', '.remove-product-btn', function() {
            $(this).closest('.product-item').remove();
            updateTotal();
        });
        
        // Gestionnaire pour la sélection de produit (délégation d'événement)
        $('#products-container').on('change', '.product-select', function() {
            const productId = $(this).val();
            const itemContainer = $(this).closest('.product-item');
            const variationContainer = itemContainer.find('.variation-container');
            const variationSelect = itemContainer.find('.variation-select');
            const priceInput = itemContainer.find('.item-price');
            
            if (!productId) {
                variationContainer.hide();
                variationSelect.empty();
                priceInput.val('');
                updateItemSubtotal(itemContainer);
                return;
            }
            
            const productOption = $(this).find('option:selected');
            const productType = productOption.data('type');
            const productPrice = productOption.data('price');
            const hasVariations = productOption.data('has-variations') === true;
            
            // Réinitialiser le prix d'achat
            priceInput.val(productPrice || '');
            
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
            
            if (variationId) {
                // Trouver le produit et la variation
                const productId = itemContainer.find('.product-select').val();
                const product = products.find(p => p.id == productId);
                const variation = product.variations.find(v => v.id == variationId);
                
                if (variation) {
                    priceInput.val(variation.prix_achat || product.prix_achat || '');
                }
            }
            
            updateItemSubtotal(itemContainer);
        });
        
        // Gestionnaire pour les changements de quantité et de prix
        $('#products-container').on('input', '.item-quantity, .item-price', function() {
            const itemContainer = $(this).closest('.product-item');
            updateItemSubtotal(itemContainer);
        });
    });
    
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
                const attributeNames = variation.attribute_values.map(av => {
                    return av.value || av.name;
                }).join(' - ');
                
                variationName += ' (' + attributeNames + ')';
            }
            
            selectElement.append(`<option value="${variation.id}" data-price="${variation.prix_achat || product.prix_achat || ''}">${variationName}</option>`);
        });
    }
    
    // Fonction pour mettre à jour le sous-total d'un élément
    function updateItemSubtotal(itemContainer) {
        const quantity = parseFloat(itemContainer.find('.item-quantity').val()) || 0;
        const price = parseFloat(itemContainer.find('.item-price').val()) || 0;
        const subtotal = quantity * price;
        
        itemContainer.find('.item-subtotal').val(subtotal.toFixed(2) + ' Dt');
        
        updateTotal();
    }
    
    // Fonction pour mettre à jour le total général
    function updateTotal() {
        let total = 0;
        
        $('.product-item').each(function() {
            const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            total += quantity * price;
        });
        
        $('#total-amount').text(total.toFixed(2));
    }
</script>
@endsection