@extends('layouts.admin')

@section('content')
<div class="row mb-3">
    <div class="col-lg-12 margin-tb">
        <div class="float-left">
            <h2>Détails du produit</h2>
        </div>
        <div class="float-right">
            <a class="btn btn-primary" href="{{ route('products.index') }}">Retour</a>
            <a class="btn btn-warning" href="{{ route('products.edit', $product->id) }}">Modifier</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>{{ $product->name }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Type:</strong>
                        {{ $product->type == 0 ? 'Produit simple' : 'Produit variable' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Référence:</strong>
                        {{ $product->reference }}
                    </div>
                    <div class="col-md-6">
                        <strong>Catégorie:</strong>
                        {{ $product->categorie->name ??  'Non définie' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Fournisseur:</strong>
                        {{  $product->provider->company ?? 'Non défini' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Quantité minimum:</strong>
                        {{ $product->min_qty }}
                    </div>
                </div>

                @if($product->isSimple())
                <div class="row mt-4">
                    <div class="col-md-6">
                        <strong>Prix d'achat:</strong>
                        {{ number_format($product->prix_achat, 2) }} Dt
                    </div>
                    <div class="col-md-6">
                        <strong>Prix HT:</strong>
                        {{ number_format($product->prix_ht, 2) }} Dt
                    </div>
                    <div class="col-md-6">
                        <strong>Prix TTC:</strong>
                        {{ number_format($product->prix_ttc, 2) }} Dt
                    </div>
                    <div class="col-md-6">
                        <strong>TVA:</strong>
                        {{ number_format($product->tva, 2) }} %
                    </div>
                </div>
                @endif

                <div class="row mt-4">
                    <div class="col-12">
                        <strong>Description:</strong>
                        <div class="mt-2">
                            {!! $product->description !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($product->isVariable() && $product->variations->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h4>Variations du produit</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Variation</th>
                                <th>Référence</th>
                                <th>Prix d'achat</th>
                                <th>Prix HT</th>
                                <th>Prix TTC</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->variations as $variation)
                            <tr>
                                <td>{{ $variation->getFormattedName() }}</td>
                                <td>{{ $variation->reference }}</td>
                                <td>{{ number_format($variation->prix_achat, 2) }} Dt</td>
                                <td>{{ number_format($variation->prix_ht, 2) }} Dt</td>
                                <td>{{ number_format($variation->prix_ttc, 2) }} Dt</td>
                                <td>{{ $variation->stock_quantity }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Images du produit</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($product->images->count() > 0)
                        @foreach($product->images as $image)
                        <div class="col-6 mb-3">
                            <div class="card {{ $image->is_main ? 'border-primary' : '' }}">
                                <img src="{{ asset(  'produits/'. $image->path) }}" class="card-img-top" alt="{{ $product->name }}">
                                <div class="card-body p-2">
                                    @if(!$image->is_main)
                                    <form action="{{ route('products.set-main-image', $image->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-info">Définir principale</button>
                                    </form>
                                    @else
                                    <span class="badge badge-primary">Image principale</span>
                                    @endif
                                    
                                    <form action="{{ route('products.delete-image', $image->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette image?')">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                    <div class="col-12">
                        <div class="alert alert-info">
                            Aucune image disponible pour ce produit.
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection