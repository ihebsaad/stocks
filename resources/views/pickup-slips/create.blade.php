@extends('layouts.admin')

@section('title', 'Nouveau bon de ramassage')
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
</style>

@endsection
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="float-left">
            <h2>Nouveau bon de ramassage</h2>
        </div>
        <div class="float-right">
            <a class="btn btn-primary" href="{{ route('pickup.index') }}"> Retour</a>
        </div>
    </div>
</div>

<form action="{{ route('pickup.store') }}" method="POST" id="pickupSlipForm">
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
                        <input type="text" name="reference" class="form-control" value="BR-{{ date('YmdHis') }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <strong>Société de livraison:</strong>
                        <select name="delivery_company_id" class="form-control" required>
                            <option value="">Choisir la société</option>
                            @foreach($deliveryCompanies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>


        <div class="  d-flex justify-content-between align-items-center">
            <h3>Scanner de codes à barres</h3>
            <div class="scanner-controls">
                <input type="text" id="barcode-input" class="form-control d-inline-block" 
                       placeholder="Scanner ou saisir le code à barres" style="width: 300px;">
                <button type="button" class="btn btn-info ml-2" id="scan-btn">
                    <i class="fas fa-barcode"></i> Scanner
                </button>
            </div>
        </div>
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>liste des colis</h4>
        </div>
        <div class="card-body">
            <div id="scanner-messages" class="mb-3"></div>
            
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
        <button type="submit" class="btn btn-primary">Enregistrer le bon de ramassage</button>
    </div>
</form>

<!-- Input cachés pour les colis sélectionnés -->

@endsection

@section('footer-scripts')
<script>
    // Variables globales
    let selectedParcels = [];
    let currentDeliveryCompanyId = null;
    
    $(document).ready(function() {
        // Gestionnaire pour le changement de société de livraison
        $('select[name="delivery_company_id"]').change(function() {
            currentDeliveryCompanyId = $(this).val();
            // Réinitialiser la liste des colis si la société change
            if (selectedParcels.length > 0) {
                if (confirm('Changer la société de livraison effacera la liste actuelle des colis. Continuer?')) {
                    selectedParcels = [];
                    updateParcelsTable();
                    updateSelectedParcelsInputs();
                } else {
                    // Restaurer la valeur précédente
                    $(this).val(currentDeliveryCompanyId);
                }
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
                const row = `
                    <tr>
                        <td>${parcel.reference}</td>
                        <td>${parcel.nom_client}</td>
                        <td>${parcel.tel_l}</td>
                        <td>${parcel.gov_l}</td>
                        <td>${parcel.adresse_l}</td>
                        <td>${parcel.cod} Dt</td>
                        <td>
                            <span class="badge badge-${getStatusBadgeClass(parcel.status)}">${parcel.status}</span>
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