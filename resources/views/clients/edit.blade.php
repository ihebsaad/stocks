@extends('layouts.admin')

@section('title', 'Modifier client - ' . $client->full_name)

@section('styles')
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
    .form-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 1px solid #f0f0f0;
    }
    .form-card h5 {
        color: #333;
        font-weight: 600;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
        display: block;
    }
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 14px;
        transition: border-color 0.3s;
    }
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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
        margin-right: 10px;
    }
    .btn-back:hover {
        background: #5a6268;
        color: white;
        text-decoration: none;
    }
    .btn-save {
        background: #28a745;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
    }
    .btn-save:hover {
        background: #218838;
        color: white;
    }
    .btn-cancel {
        background: #dc3545;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
        margin-left: 10px;
    }
    .btn-cancel:hover {
        background: #c82333;
        color: white;
        text-decoration: none;
    }
    .form-actions {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-top: 30px;
        text-align: center;
    }
    .required {
        color: #dc3545;
    }
    .form-row {
        display: flex;
        gap: 20px;
    }
    .form-row .form-group {
        flex: 1;
    }
    .alert {
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 5px;
    }
    .is-invalid {
        border-color: #dc3545;
    }
    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 5px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Boutons navigation -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('clients.show', $client->id) }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour aux détails
            </a>
            <a href="{{ route('clients.index') }}" class="btn-back">
                <i class="fas fa-list"></i> Liste des clients
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
                <h2>Modifier - {{ $client->full_name }}</h2>
                <div class="client-id">Client #{{ $client->id }}</div>
            </div>
        </div>
    </div>

    <!-- Messages d'erreur -->
    @if($errors->any())
        <div class="alert alert-danger">
            <h6><i class="fas fa-exclamation-triangle"></i> Erreurs de validation :</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulaire de modification -->
    <form action="{{ route('clients.update', $client->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Informations personnelles -->
            <div class="col-md-6">
                <div class="form-card">
                    <h5><i class="fas fa-user"></i> Informations personnelles</h5>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">Prénom <span class="required">*</span></label>
                            <input type="text" 
                                   class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name', $client->first_name) }}" 
                                   required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Nom <span class="required">*</span></label>
                            <input type="text" 
                                   class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="{{ old('last_name', $client->last_name) }}" 
                                   required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Téléphone principal <span class="required">*</span></label>
                            <input type="tel" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $client->phone) }}" 
                                   required>
                            <div class="form-text">Format : 12 345 678 ou 20 123 456</div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="phone2">Téléphone secondaire</label>
                            <input type="tel" 
                                   class="form-control @error('phone2') is-invalid @enderror" 
                                   id="phone2" 
                                   name="phone2" 
                                   value="{{ old('phone2', $client->phone2) }}">
                            <div class="form-text">Optionnel</div>
                            @error('phone2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations de localisation -->
            <div class="col-md-6">
                <div class="form-card">
                    <h5><i class="fas fa-map-marker-alt"></i> Localisation</h5>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">Ville <span class="required">*</span></label>
                            <input type="text" 
                                   class="form-control @error('city') is-invalid @enderror" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $client->city) }}" 
                                   required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="delegation">Délégation <span class="required">*</span></label>
                            <input type="text" 
                                   class="form-control @error('delegation') is-invalid @enderror" 
                                   id="delegation" 
                                   name="delegation" 
                                   value="{{ old('delegation', $client->delegation) }}" 
                                   required>
                            @error('delegation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Adresse complète</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" 
                                  name="address" 
                                  rows="3">{{ old('address', $client->address) }}</textarea>
                        <div class="form-text">Adresse détaillée pour la livraison</div>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="postal_code">Code postal</label>
                        <input type="text" 
                               class="form-control @error('postal_code') is-invalid @enderror" 
                               id="postal_code" 
                               name="postal_code" 
                               value="{{ old('postal_code', $client->postal_code) }}"
                               maxlength="4">
                        <div class="form-text">Format : 4 chiffres (ex: 1000)</div>
                        @error('postal_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes additionnelles -->
        <div class="row">
            <div class="col-12">
                <div class="form-card">
                    <h5><i class="fas fa-sticky-note"></i> Notes additionnelles</h5>
                    
                    <div class="form-group">
                        <label for="notes">Notes sur le client</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" 
                                  name="notes" 
                                  rows="4">{{ old('notes', $client->notes) }}</textarea>
                        <div class="form-text">Informations supplémentaires, préférences, historique...</div>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
            <a href="{{ route('clients.show', $client->id) }}" class="btn-cancel">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
</div>
@endsection

@section('footer-scripts')
<script>
$(function() {
    // Validation côté client
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Vérifier les champs requis
        $('input[required], textarea[required]').each(function() {
            if ($(this).val().trim() === '') {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Validation du téléphone
        const phonePattern = /^[0-9\s]{8,12}$/;
        if (!phonePattern.test($('#phone').val())) {
            $('#phone').addClass('is-invalid');
            isValid = false;
        }
        
        // Validation du code postal (optionnel)
        if ($('#postal_code').val() && !/^\d{4}$/.test($('#postal_code').val())) {
            $('#postal_code').addClass('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        }
    });
    
    // Supprimer la classe d'erreur lors de la saisie
    $('.form-control').on('input', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Formatage automatique du téléphone
    $('#phone, #phone2').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length <= 8) {
            value = value.replace(/(\d{2})(\d{3})(\d{3})/, '$1 $2 $3');
        } else {
            value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{2})/, '$1 $2 $3 $4');
        }
        $(this).val(value);
    });
    
    // Formatage du code postal
    $('#postal_code').on('input', function() {
        $(this).val($(this).val().replace(/\D/g, ''));
    });
    
    // Animation d'entrée
    $('.form-card').each(function(index) {
        $(this).delay(index * 100).fadeIn();
    });
});
</script>
@endsection