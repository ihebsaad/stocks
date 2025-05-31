@extends('layouts.admin')

@section('title', 'Modifier un colis')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2>Modifier le colis</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('orders.edit', $parcel->order_id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la commande
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('parcels.update', $parcel->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="nom_client" class="form-label">Nom du client *</label>
                        <input type="text" name="nom_client" id="nom_client" class="form-control @error('nom_client') is-invalid @enderror"
                               value="{{ old('nom_client', $parcel->nom_client) }}" required>
                        @error('nom_client')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="tel_l" class="form-label">Téléphone *</label>
                        <input type="text" name="tel_l" id="tel_l" class="form-control @error('tel_l') is-invalid @enderror"
                               value="{{ old('tel_l', $parcel->tel_l) }}" required>
                        @error('tel_l')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="tel2_l" class="form-label">Téléphone 2</label>
                        <input type="text" name="tel2_l" id="tel2_l" class="form-control @error('tel2_l') is-invalid @enderror"
                               value="{{ old('tel2_l', $parcel->tel2_l) }}">
                        @error('tel2_l')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="gov_l" class="form-label">Gouvernorat *</label>
                        @error('gov_l')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <select  name="gov_l" id="gov_l" class="form-control @error('gov_l') is-invalid @enderror"  required>
                            @foreach($delegations as $delegation)
                                <option value="{{ $delegation }}"{{ $parcel->gov_l == $delegation ? 'selected' : '' }}>
                                    {{ $delegation }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label for="adresse_l" class="form-label">Adresse *</label>
                        <input type="text" name="adresse_l" id="adresse_l" class="form-control @error('adresse_l') is-invalid @enderror"
                               value="{{ old('adresse_l', $parcel->adresse_l) }}" required>
                        @error('adresse_l')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="cod" class="form-label"> COD *</label>
                        <input type="text" name="cod" id="cod" class="form-control @error('cod') is-invalid @enderror"
                               value="{{ old('cod', $parcel->cod) }}" required>
                        @error('cod')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="nb_piece" class="form-label">Nombre de pièces</label>
                        <input type="text" name="nb_piece" id="nb_piece" class="form-control @error('nb_piece') is-invalid @enderror"
                               value="{{ old('nb_piece', $parcel->nb_piece) }}">
                        @error('nb_piece')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="libelle" class="form-label">Libellé</label>
                        <input type="text" name="libelle" id="libelle" class="form-control @error('libelle') is-invalid @enderror"
                               value="{{ old('libelle', $parcel->libelle) }}">
                        @error('libelle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="remarque" class="form-label">Remarque</label>
                    <textarea name="remarque" id="remarque" rows="3" class="form-control @error('remarque') is-invalid @enderror">{{ old('remarque', $parcel->remarque) }}</textarea>
                    @error('remarque')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label for="service" class="form-label">Service *</label>
                    <select name="service" id="service" class="form-control @error('service') is-invalid @enderror" required>
                        <option value="Livraison" {{ old('service', $parcel->service) === 'Livraison' ? 'selected' : '' }}>Livraison</option>
                        <option value="Echange" {{ old('service', $parcel->service) === 'Echange' ? 'selected' : '' }}>Échange</option>
                    </select>
                    @error('service')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Mettre à jour</button>
                    <a href="{{ route('parcels.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
