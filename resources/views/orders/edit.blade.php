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
    /*
    $(document).ready(function() {
        // Initialisation de Select2
        $('.select2').select2({
            placeholder: 'Rechercher un client par téléphone, prénom ou nom',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("clients.search") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });
        
        // Remplir les informations client lorsque sélectionné
        $('#client_search').on('select2:select', function(e) {
            var data = e.params.data;
            $('#client_id').val(data.id);
            
            if (data.client_details) {
                var client = data.client_details;
                $('#phone').val(client.phone);
                $('#phone2').val(client.phone2);
                $('#first_name').val(client.first_name);
                $('#last_name').val(client.last_name);
                $('#city').val(client.city);
                $('#delegation').val(client.delegation);
                $('#address').val(client.address);
                $('#postal_code').val(client.postal_code);
            }
        });
        
        // Recherche client par téléphone
        $('#phone').on('blur', function() {
            var phone = $(this).val();
            if (phone.length >= 8) {
                $.ajax({
                    url: '{{ route("clients.check-phone") }}',
                    type: 'GET',
                    data: { phone: phone },
                    success: function(response) {
                        if (response.exists) {
                            // Remplir les champs avec les informations du client
                            $('#client_id').val(response.client.id);
                            $('#phone2').val(response.client.phone2);
                            $('#first_name').val(response.client.first_name);
                            $('#last_name').val(response.client.last_name);
                            $('#city').val(response.client.city);
                            $('#delegation').val(response.client.delegation);
                            $('#address').val(response.client.address);
                            $('#postal_code').val(response.client.postal_code);
                            
                            // Notification
                            alert('Client existant trouvé et informations remplies.');
                        } else {
                            // Réinitialiser l'ID client pour créer un nouveau
                            $('#client_id').val('');
                        }
                    }
                });
            }
        });
        

    });
*/

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
});

  
        
</script>
@endsection