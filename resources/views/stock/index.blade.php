<!-- 1. Vue Blade mise à jour (stock/index.blade.php) -->
@extends('layouts.admin')

@section('styles')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@endsection

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
            <table class="table table-bordered table-hover" id="stock-entries-table">
                <thead class="thead-light">
                    <tr>
                        <th>N°</th>
                        <th>Date</th>
                        <th>Référence</th>
                        <th>Description</th>
                        <th>Nombre de produits</th>
                        <th>Total</th>
                        <th class="no-sort" style="width:10%">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
<!-- DataTables & Plugins -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>

<script>
$(function() {
    $('#stock-entries-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '{{ route("stock.entries.list") }}',
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'date_formatted', name: 'date' },
            { data: 'reference', name: 'reference' },
            { data: 'description', name: 'description' },
            { data: 'products_count', name: 'products_count', orderable: false, searchable: false },
            { data: 'total_formatted', name: 'total', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
        },
        order: [[1, 'desc']], // Trier par date décroissante
        columnDefs: [
            { targets: 'no-sort', orderable: false }
        ]
    });
});
</script>
@endsection