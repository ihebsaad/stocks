@extends('layouts.admin')

@section('title', 'Gestion des sociétés de livraison')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
    #delivery-companies-table {
        width: 100%;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Liste des sociétés de livraison</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('delivery-companies.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle société de livraison
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive-">
                <table class="table table-bordered table-striped" id="delivery-companies-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prix de livraison</th>
                            <th>Téléphone</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function() {
    let table = $('#delivery-companies-table').DataTable({
        processing: true,
        serverSide: true,
        type: 'GET',
        dataType: "json", 
        ajax: {
            url: "{{ route('delivery-companies.getData') }}"
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'delivery_price_formatted', name: 'delivery_price' },
            { data: 'phone', name: 'phone' },
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
        }
    });
});
</script>
@endsection