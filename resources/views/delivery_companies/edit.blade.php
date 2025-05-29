@extends('layouts.admin')

@section('title', 'Modifier une société de livraison')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2>Modifier la société de livraison</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('delivery-companies.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('delivery-companies.update', $deliveryCompany->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name" class="form-label">Nom de la société <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $deliveryCompany->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="delivery_price" class="form-label">Prix de livraison (DT) <span class="text-danger">*</span></label>
                            <input type="number" step="0.001" min="0" class="form-control @error('delivery_price') is-invalid @enderror" id="delivery_price" name="delivery_price" value="{{ old('delivery_price', $deliveryCompany->delivery_price) }}" required>
                            @error('delivery_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="manager_name" class="form-label">Nom du responsable</label>
                            <input type="text" class="form-control @error('manager_name') is-invalid @enderror" id="manager_name" name="manager_name" value="{{ old('manager_name', $deliveryCompany->manager_name) }}">
                            @error('manager_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>                    
                </div>
                
                <div class="row mb-3">

                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $deliveryCompany->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="phone" class="form-label">Adresse</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="phone" value="{{ old('address', $deliveryCompany->address) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror" id="is_active" name="is_active" value="1" {{ old('is_active', $deliveryCompany->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Société active
                                </label>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Cochez cette case pour activer la société de livraison</small>
                        </div>
                    </div>
                </div>

                <!-- Configuration API -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-cog"></i> Configuration API
                        </h5>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="api_url_prod" class="form-label">URL API Production</label>
                            <input type="url" class="form-control @error('api_url_prod') is-invalid @enderror" id="api_url_prod" name="api_url_prod" value="{{ old('api_url_prod', $deliveryCompany->api_url_prod) }}" placeholder="https://api.exemple.com">
                            @error('api_url_prod')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">URL de l'API en environnement de production</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="api_url_dev" class="form-label">URL API Développement</label>
                            <input type="url" class="form-control @error('api_url_dev') is-invalid @enderror" id="api_url_dev" name="api_url_dev" value="{{ old('api_url_dev', $deliveryCompany->api_url_dev) }}" placeholder="https://dev-api.exemple.com">
                            @error('api_url_dev')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">URL de l'API en environnement de développement</small>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="code_api" class="form-label">Code API</label>
                            <input type="text" class="form-control @error('code_api') is-invalid @enderror" id="code_api" name="code_api" value="{{ old('code_api', $deliveryCompany->code_api) }}" placeholder="CODE_API_123">
                            @error('code_api')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Code d'identification pour l'API</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cle_api" class="form-label">Clé API</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('cle_api') is-invalid @enderror" id="cle_api" name="cle_api" value="{{ old('cle_api', $deliveryCompany->cle_api) }}" placeholder="Clé secrète de l'API">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('cle_api')">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                                @error('cle_api')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Clé secrète pour l'authentification API</small>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                        <a href="{{ route('delivery-companies.index') }}" class="btn btn-secondary">
                            Annuler
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById('toggleIcon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

@endsection