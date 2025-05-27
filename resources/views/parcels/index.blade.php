@extends('layouts.admin')

@section('title', 'Gestion des commandes')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
    .status-badge {
        display: inline-block;
        padding: 0.25em 0.6em;
        font-size: 0.75em;
        font-weight: 700;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }
    .status-draft { background-color: #6c757d; color: white; }
    .status-pending { background-color: #ffc107; color: black; }
    .status-pickup { background-color: #17a2b8; color: white; }
    .status-no_response { background-color: #dc3545; color: white; }
    .status-cancelled { background-color: #6c757d; color: white; }
    .status-in_delivery { background-color: #007bff; color: white; }
    .status-completed { background-color: #28a745; color: white; }
    
    .filter-row {
        padding: 10px 0;
        margin-bottom: 15px;
    }
    #orders-table{
        width:100%;
    }
</style>
@endsection

@section('content')
<div class="container-">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Liste des Colis</h2>
        </div>
        <div class="col-md-6 text-end">
 
        </div>
    </div>
     
            <div class="table-responsive-">
                <table class="table table-bordered table-striped" id="parcels-table">
                    <thead>
                        <tr>
                            <th>Rérérence</th>
                            <th>Date de création</th>
                            <th>Client</th>
                            <th>Statut</th>
                            <th>Service</th>
                            <th>Société de livraison</th>
                            <th>Commande</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function() {
    let table = $('#parcels-table').DataTable({
        processing: true,
        serverSide: true,
        type: 'GET',
        dataType: "json", 
        ajax: {
            url: "{{ route('parcels.getParcels') }}",
        },
        columns: [
            { data: 'reference', name: 'reference' },
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'client', name: 'client', orderable: false, searchable: false },
            { data: 'dernier_etat', name: 'dernier_etat' },
            { data: 'service_type', name: 'service_type' },
            { data: 'delivery_company', name: 'delivery_company' },
            { data: 'order_id', name: 'order_id' },
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