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
    .bg-1{ background-color: #0da598; color: white;   }
    .bg-2{ background-color: #ef6f28; color: white;   }
    .bg-3{ background-color: #227ac2; color: white;   }
    .bg-4{ background-color: #6c757d; color: white;   }
    .bg-5{ background-color: #fd9883; color: white;   }
    .status-draft { background-color: #6c757d; color: white; }
    .status-pending { background-color: #ffc107; color: black; }
    .status-production { background-color: #17a2b8; color: white; }
    .status-no_response { background-color: #dc3545; color: white; }
    .status-not_available { background-color: #ee7631; color: white; }
    .status-cancelled { background-color: #141414; color: white; }
    .status-confirmed,.status-completed  { background-color: #28a745; color: white; }
    
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
            <h2>Liste des Commandes</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle commande
            </a>
        </div>
    </div>  
 
            <div class="row filter-row">
                <div class="col-md-3">
                    <label for="status-filter">Filtrer par statut:</label>
                    <select id="status-filter" class="form-control">
                        <option value="">Tous les statuts</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="delivery-company-filter">Filtrer par société de livraison:</label>
                    <select id="delivery-company-filter" class="form-control">
                        <option value="">Toutes les sociétés</option>
                        @foreach($deliveryCompanies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="user-filter">Filtrer par utilisateur:</label>
                    <select id="user-filter" class="form-control">
                        <option value="">Tous les utilisateurs</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
  
            <div class="table-responsive-">
                <table class="table table-bordered table-striped" id="orders-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Service</th>
                            <th>Société de livraison</th>
                            <th>Statut</th>
                            <th>Date de création</th>
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
    let table = $('#orders-table').DataTable({
        processing: true,
        serverSide: true,
        type: 'GET',
        dataType: "json", 
        ajax: {
            url: "{{ route('orders.getOrders') }}",
            data: function(d) {
                d.status = $('#status-filter').val();
                d.delivery_company = $('#delivery-company-filter').val();
                d.user_id = $('#user-filter').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'client_name', name: 'client_name' },
            { data: 'service_type_formatted', name: 'service_type_formatted' },
            { data: 'delivery_company_info', name: 'delivery_company_info' },
            { data: 'status_formatted', name: 'status_formatted' },
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
        }
    });
    
    // cacher les commandes ayant coli
    table.on('draw', function () {
        $('#orders-table tbody tr').each(function () {
            if ($(this).find('.hidden').length > 0) {
                $(this).hide();
            }
        });
    });
    
    // Appliquer les filtres lorsque les valeurs changent
    $('#status-filter, #delivery-company-filter , #user-filter').change(function() {
        table.draw();
    });
});
</script>
@endsection