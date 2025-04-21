@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="float-left">
            <h2>Gestion des entrées de stock</h2>
        </div>
        <div class="float-right">
            <a class="btn btn-success" href="{{ route('stock.entries.create') }}">
                <i class="fas fa-plus"></i> Nouvelle entrée de stock
            </a>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h4>Liste des entrées de stock</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>N°</th>
                        <th>Date</th>
                        <th>Référence</th>
                        <th>Description</th>
                        <th>Nombre de produits</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entries as $entry)
                    <tr>
                        <td>{{ $entry->id }}</td>
                        <td>{{ $entry->date->format('d/m/Y') }}</td>
                        <td>{{ $entry->reference }}</td>
                        <td>{{ $entry->description }}</td>
                        <td>{{ $entry->items->count() }}</td>
                        <td>{{ number_format($entry->getTotal(), 2, ',', ' ') }} Dt</td>
                        <td>
                            <a href="{{ route('stock.entries.show', $entry->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection