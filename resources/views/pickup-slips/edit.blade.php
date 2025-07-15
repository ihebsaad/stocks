@extends('layouts.admin')

@section('title', 'Modifier le bon de ramassage')

@section('styles')
<style>
    .scanner-controls {
        display: flex;
        align-items: center;
    }
    
    #barcode-input {
        font-family: monospace;
        font-size: 14px;
        text-align: center;
    }
    
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    .badge {
        font-size: 0.85em;
    }
    
    .alert {
        margin-bottom: 0;
    }
    
    #scanner-messages {
        min-height: 20px;
    }
    
    .remove-parcel-btn {
        padding: 0.25rem 0.5rem;
    }
    
    .existing-parcel {
        background-color: #f8f9fa;
    }
    
    .new-parcel {
        background-color: #e8f5e8;
    }
    
    .status-badge {
        min-width: 80px;
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="float-left">
            <h2>Modifier le bon de ramassage: {{ $pickupSlip->reference }}</h2>
        </div>
        <div class="float-right">
            <a class="btn btn-primary" href="{{ route('pickup.index') }}">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a class="btn btn-info" href="{{ route('pickup.show', $pickupSlip->id) }}">
                <i class="fas fa-eye"></i> Voir
            </a>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Erreurs:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('pickup.update', $pickupSlip->id) }}" method="POST" id="pickupSlipForm">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-header">
            <h4>Informations générales</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <strong>Date:</strong>
                        <input type="date" name="date" class="form-control" 
                               value="{{ old('date', $pickupSlip->date) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <strong>Référence:</strong>
                        <input type="text" name="reference" class="form-control" 
                               value="{{ old('reference', $pickupSlip->reference) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <strong>Société de livraison:</strong>
                        <select name="delivery_company_id" class="form-control" required>
                            <option value="">Choisir la société</option>
                            @foreach($deliveryCompanies as $company)
                                <option value="{{ $company->id }}" 
                                        {{ old('delivery_company_id', $pickupSlip->delivery_company_id) == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <strong>Statut:</strong>
                        <span class="badge badge-{{ $pickupSlip->status === 'pending' ? 'warning' : 
                                                   ($pickupSlip->status === 'completed' ? 'success' : 'info') }}">
                            {{ ucfirst($pickupSlip->status) }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <strong>Créé par:</strong>
                        {{ $pickupSlip->user->name ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Scanner de codes à barres</h4>
            <div class="scanner-controls">
                <input type="text" id="barcode-input" class="form-control d-inline-block" 
                       placeholder="Scanner ou saisir le code à barres" style="width: 300px;">
                <button type="button" class="btn btn-info ml-2" id="scan-btn">
                    <i class="fas fa-barcode"></i> Scanner
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="scanner-messages" class="mb-3"></div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Légende:</strong> 
                <span class="badge badge-light ml-2">Gris = Colis existant</span>
                <span class="badge badge-success ml-2">Vert = Nouveau colis ajouté</span>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h4>Liste des colis</h4>
        </div>
        <div class="card-body">
            <div id="parcels-container">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="parcels-table">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Client</th>
                                <th>Téléphone</th>
                                <th>Gouvernorat</th>
                                <th>Adresse</th>
                                <th>COD</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="parcels-tbody">
                            <!-- Les colis seront ajoutés ici dynamiquement -->
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="text-right mt-3">
                <strong>Total colis: <span id="total-parcels">0</span></strong>
            </div>
        </div>
    </div>

    <div id="selected-parcels-inputs"></div>

    <div class="col-xs-12 col-sm-12 col-lg-12 mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Mettre à jour le bon de ramassage
        </button>
        <a href="{{ route('pickup.index') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i> Annuler
        </a>
    </div>
</form>
@endsection

@section('footer-scripts')
<script>
    // Variables globales
    let selectedParcels = [];
    let currentDeliveryCompanyId = null;
    let existingParcelIds = [];
    
    $(document).ready(function() {
        // Initialiser avec les colis existants
        initializeExistingParcels();
        
        // Définir la société de livraison actuelle
        currentDeliveryCompanyId = $('select[name="delivery_company_id"]').val();
        
        // Gestionnaire pour le changement de société de livraison
        $('select[name="delivery_company_id"]').change(function() {
            const newDeliveryCompanyId = $(this).val();
            
            if (selectedParcels.length > 0 && newDeliveryCompanyId !== currentDeliveryCompanyId) {
                if (confirm('Changer la société de livraison effacera les nouveaux colis ajoutés. Continuer?')) {
                    // Garder seulement les colis existants
                    selectedParcels = selectedParcels.filter(p => existingParcelIds.includes(p.id));
                    currentDeliveryCompanyId = newDeliveryCompanyId;
                    updateParcelsTable();
                    updateSelectedParcelsInputs();
                } else {
                    // Restaurer la valeur précédente
                    $(this).val(currentDeliveryCompanyId);
                }
            } else {
                currentDeliveryCompanyId = newDeliveryCompanyId;
            }
        });
        
        // Gestionnaire pour le scanner de codes à barres
        $('#barcode-input').keypress(function(e) {
            if (e.which === 13) { // Touche Entrée
                e.preventDefault();
                scanBarcode();
            }
        });
        
        $('#scan-btn').click(function() {
            scanBarcode();
        });
        
        // Gestionnaire pour supprimer un colis de la liste
        $('#parcels-tbody').on('click', '.remove-parcel-btn', function() {
            const parcelId = $(this).data('parcel-id');
            removeParcelFromList(parcelId);
        });
        
        // Focus automatique sur le champ de saisie
        $('#barcode-input').focus();
    });
    
    // Fonction pour initialiser avec les colis existants
    function initializeExistingParcels() {
        const existingParcels = @json($pickupSlip->parcels);
        
        existingParcels.forEach(parcel => {
            selectedParcels.push(parcel);
            existingParcelIds.push(parcel.id);
        });
        
        updateParcelsTable();
        updateSelectedParcelsInputs();
    }
    
    // Fonction pour scanner/rechercher un code à barres
    function scanBarcode() {
        const barcode = $('#barcode-input').val().trim();
        
        if (!barcode) {
            showMessage('Veuillez saisir un code à barres', 'warning');
            return;
        }
        
        if (!currentDeliveryCompanyId) {
            showMessage('Veuillez sélectionner une société de livraison d\'abord', 'warning');
            return;
        }
        
        // Vérifier si le colis est déjà dans la liste
        if (selectedParcels.find(p => p.reference === barcode)) {
            showMessage('Ce colis est déjà dans la liste', 'warning');
            $('#barcode-input').val('').focus();
            return;
        }
        
        // Rechercher le colis dans la base de données
        $.ajax({
            url: '{{ route("parcels.search") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                _token: '{{ csrf_token() }}',
                barcode: barcode,
                delivery_company_id: currentDeliveryCompanyId
            },
            beforeSend: function() {
                $('#scan-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Recherche...');
            },
            success: function(response) {
                if (response.success && response.parcel) {
                    addParcelToList(response.parcel);
                    showMessage('Colis ajouté avec succès', 'success');
                    $('#barcode-input').val('').focus();
                } else {
                    showMessage(response.message || 'Colis non trouvé', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Erreur lors de la recherche';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showMessage(errorMessage, 'error');
            },
            complete: function() {
                $('#scan-btn').prop('disabled', false).html('<i class="fas fa-barcode"></i> Scanner');
                $('#barcode-input').focus();
            }
        });
    }
    
    // Fonction pour ajouter un colis à la liste
    function addParcelToList(parcel) {
        selectedParcels.push(parcel);
        updateParcelsTable();
        updateSelectedParcelsInputs();
    }
    
    // Fonction pour supprimer un colis de la liste
    function removeParcelFromList(parcelId) {
        // Vérifier si c'est un colis existant
        if (existingParcelIds.includes(parseInt(parcelId))) {
            if (!confirm('Ce colis faisait partie du bon de ramassage original. Êtes-vous sûr de vouloir le supprimer?')) {
                return;
            }
        }
        
        selectedParcels = selectedParcels.filter(p => p.id != parcelId);
        updateParcelsTable();
        updateSelectedParcelsInputs();
        showMessage('Colis supprimé de la liste', 'info');
    }
    
    // Fonction pour mettre à jour le tableau des colis
    function updateParcelsTable() {
        const tbody = $('#parcels-tbody');
        tbody.empty();
        
        if (selectedParcels.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="8" class="text-center text-muted">Aucun colis ajouté</td>
                </tr>
            `);
        } else {
            selectedParcels.forEach(parcel => {
                const isExisting = existingParcelIds.includes(parcel.id);
                const rowClass = isExisting ? 'existing-parcel' : 'new-parcel';
                
                const row = `
                    <tr class="${rowClass}">
                        <td>${parcel.reference}</td>
                        <td>${parcel.nom_client}</td>
                        <td>${parcel.tel_l}</td>
                        <td>${parcel.gov_l}</td>
                        <td>${parcel.adresse_l}</td>
                        <td>${parcel.cod} Dt</td>
                        <td>
                            <span class="badge badge-${getStatusBadgeClass(parcel.status)} status-badge">
                                ${parcel.status}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-parcel-btn" 
                                    data-parcel-id="${parcel.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }
        
        // Mettre à jour le compteur
        $('#total-parcels').text(selectedParcels.length);
    }
    
    // Fonction pour mettre à jour les inputs cachés
    function updateSelectedParcelsInputs() {
        const container = $('#selected-parcels-inputs');
        container.empty();
        
        selectedParcels.forEach((parcel, index) => {
            container.append(`<input type="hidden" name="parcels[${index}]" value="${parcel.id}">`);
        });
    }
    
    // Fonction pour obtenir la classe CSS du badge selon le statut
    function getStatusBadgeClass(status) {
        switch(status) {
            case 'pending': return 'warning';
            case 'in_transit': return 'info';
            case 'delivered': return 'success';
            case 'returned': return 'danger';
            default: return 'secondary';
        }
    }
    
    // Fonction pour afficher les messages
    function showMessage(message, type) {
        const alertClass = type === 'error' ? 'alert-danger' : 
                          type === 'success' ? 'alert-success' : 
                          type === 'warning' ? 'alert-warning' : 'alert-info';
        
        const messageHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $('#scanner-messages').html(messageHtml);
        
        // Supprimer le message après 5 secondes
        setTimeout(() => {
            $('#scanner-messages .alert').fadeOut();
        }, 5000);
    }
    
    // Validation du formulaire avant soumission
    $('#pickupSlipForm').submit(function(e) {
        if (selectedParcels.length === 0) {
            e.preventDefault();
            showMessage('Veuillez ajouter au moins un colis avant d\'enregistrer', 'error');
            return false;
        }
        
        return true;
    });
</script>
@endsection