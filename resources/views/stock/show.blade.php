@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="float-left">
            <h2>Détails de l'entrée de stock</h2>
        </div>
        <div class="float-right">
            <a class="btn btn-primary" href="{{ route('stock.entries.index') }}"> Retour</a>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h4>Informations générales</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Date:</strong>
                {{ $entry->date->format('d/m/Y') }}
            </div>
            <div class="col-md-4">
                <strong>Référence:</strong>
                {{ $entry->reference }}
            </div>
            <div class="col-md-4">
                <strong>Description:</strong>
                <div class="input-group">
                    <textarea id="entry-description" class="form-control form-control-sm" data-entry-id="{{ $entry->id }}">{{ $entry->description }}</textarea>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-sm save-description" type="button">
                            <i class="fas fa-save"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h4>Produits</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Produit</th>
                        <th>Variation</th>
                        <th>Quantité</th>
                        <th>Prix d'achat</th>
                        <th>Sous-total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entry->items as $item)
                    <tr data-item-id="{{ $item->id }}">
                        <td>{{ $item->product->name }} ({{ $item->product->reference }})</td>
                        <td>
                            @if ($item->variation)
                                @php
                                    $attributes = [];
                                    foreach ($item->variation->attributeValues as $attributeValue) {
                                        $attributes[] = $attributeValue->attribute->name . ': ' . $attributeValue->value;
                                    }
                                @endphp
                                {{ $item->variation->reference }} ({{ implode(', ', $attributes) }})
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm quantity-input" 
                                   value="{{ $item->quantity }}" min="1" data-item-id="{{ $item->id }}">
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control form-control-sm price-input" 
                                       value="{{ $item->prix_achat }}" min="0" step="0.01" data-item-id="{{ $item->id }}">
                                <div class="input-group-append">
                                    <span class="input-group-text">Dt</span>
                                </div>
                            </div>
                        </td>
                        <td class="item-subtotal">{{ number_format($item->quantity * $item->prix_achat, 2, ',', ' ') }} Dt</td>
                        <td>
                            <button class="btn btn-primary btn-sm save-item-changes" data-item-id="{{ $item->id }}">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Total:</strong></td>
                        <td id="grand-total" colspan="2"><strong>{{ number_format($entry->getTotal(), 2, ',', ' ') }} Dt</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection

@section('footer-scripts')
<script>
    
document.addEventListener('DOMContentLoaded', function() {
    // CSRF Token pour les requêtes AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Gestion de la mise à jour de la description
    document.querySelector('.save-description').addEventListener('click', function() {
        const textarea = document.getElementById('entry-description');
        const entryId = textarea.dataset.entryId;
        const description = textarea.value.trim();
        
        updateEntryDescription(entryId, description);
    });
    
    // Gestion de la mise à jour des éléments (quantité et prix)
    document.querySelectorAll('.save-item-changes').forEach(function(button) {
        button.addEventListener('click', function() {
            const itemId = this.dataset.itemId;
            const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
            const quantityInput = row.querySelector('.quantity-input');
            const priceInput = row.querySelector('.price-input');
            
            const quantity = parseInt(quantityInput.value, 10);
            const price = parseFloat(priceInput.value);
            
            // Validation
            if (isNaN(quantity) || quantity < 1) {
                alert('Veuillez entrer une quantité valide (minimum 1)');
                return;
            }
            
            if (isNaN(price) || price < 0) {
                alert('Veuillez entrer un prix valide (minimum 0)');
                return;
            }
            
            // Mettre à jour les données
            updateItemData(itemId, quantity, price).then(() => {
                // Mettre à jour les totaux après succès
                updateTotals(itemId);
            });
        });
    });
    
    // Fonction pour mettre à jour la description de l'entrée
    async function updateEntryDescription(entryId, description) {
        alert(baseUrl);
        try {
            const response = await fetch(`${baseUrl}/stock/entries/${entryId}/update-description`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ description: description })
            });
            
            const data = await response.json();
            if (data.success) {
                // Afficher un message de succès
                showAlert('success', 'Description mise à jour avec succès');
            } else {
                showAlert('danger', 'Erreur lors de la mise à jour: ' + data.message);
            }
            
            return data;
        } catch (error) {
            console.error('Erreur AJAX:', error);
            showAlert('danger', 'Erreur de connexion');
            return { success: false, message: 'Erreur de connexion' };
        }
    }
    
    // Fonction pour mettre à jour les données d'un élément (quantité et prix)
    async function updateItemData(itemId, quantity, price) {
        try {
            const response = await fetch(`${baseUrl}/stock/entry-items/${itemId}/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ 
                    quantity: quantity,
                    prix_achat: price
                })
            });
            
            const data = await response.json();
            if (data.success) {
                showAlert('success', 'Données mises à jour avec succès');
            } else {
                showAlert('danger', 'Erreur lors de la mise à jour: ' + data.message);
            }
            
            return data;
        } catch (error) {
            console.error('Erreur AJAX:', error);
            showAlert('danger', 'Erreur de connexion');
            return { success: false, message: 'Erreur de connexion' };
        }
    }
    
    // Fonction pour mettre à jour les totaux
    async function updateTotals(itemId) {
        try {
            const response = await fetch(`${baseUrl}/stock/entries/calculate-totals/${itemId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            const data = await response.json();
            if (data.success) {
                // Mettre à jour le sous-total de l'article
                const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
                const subtotalCell = row.querySelector('.item-subtotal');
                subtotalCell.textContent = `${formatNumber(data.subtotal)} Dt`;
                
                // Mettre à jour le total général
                const grandTotalCell = document.getElementById('grand-total');
                grandTotalCell.innerHTML = `<strong>${formatNumber(data.total)} Dt</strong>`;
            }
            
            return data;
        } catch (error) {
            console.error('Erreur AJAX:', error);
            return { success: false, message: 'Erreur de connexion' };
        }
    }
    
    // Fonction pour formater un nombre
    function formatNumber(number) {
        return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(number);
    }
    
    // Fonction pour afficher une alerte temporaire
    function showAlert(type, message) {
        // Créer l'élément d'alerte
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Fermer">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        
        // Ajouter l'alerte au début de la page
        const contentDiv = document.querySelector('.content');
        contentDiv.insertBefore(alertDiv, contentDiv.firstChild);
        
        // Supprimer l'alerte après 3 secondes
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
});
</script>
@endsection