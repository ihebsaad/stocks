@extends('layouts.admin')

@section('title', 'Gestion des clients')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap5.min.css">

<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 20px;
        color: white;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .stats-card h3 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stats-card p {
        margin: 0;
        opacity: 0.9;
    }
    .stats-row {
        margin-bottom: 30px;
    }
    .filter-row {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    #clients-table {
        width: 100%;
    }
    .client-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 16px;
    }
    .client-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .client-details h6 {
        margin: 0;
        font-weight: 600;
        color: #333;
    }
    .client-details small {
        color: #666;
    }
    .phone-badge {
        background: #e3f2fd;
        color: #1976d2;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        margin-right: 5px;
    }
    .order-stats {
        text-align: center;
    }
    .order-count {
        font-size: 1.5rem;
        font-weight: bold;
        color: #28a745;
    }
    .last-order {
        font-size: 0.8rem;
        color: #666;
    }
    .location-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .city-badge {
        background: #f0f0f0;
        color: #333;
        padding: 2px 8px;
        border-radius: 8px;
        font-size: 0.8rem;
        width: fit-content;
    }
    .delegation-text {
        font-size: 0.8rem;
        color: #666;
    }
    .table-container {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-users"></i> Gestion des clients</h2>
            <!--<small class="text-muted">Gérez votre base de données clients</small>-->
        </div>
        <div class="col-md-6 text-end">
            <!--<a href="{{ route('clients.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-user-plus"></i> Nouveau client
            </a>-->
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row stats-row">
        <div class="col-md-3">
            <div class="stats-card">
                <h3 id="total-clients">{{ $totalClients }}</h3>
                <p>Total clients</p>
            </div>
        </div><!--
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h3 id="active-clients">{{ $activeClients }}</h3>
                <p>Clients actifs</p>
            </div>
        </div>-->
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <h3 id="new-clients">{{ $newClientsThisMonth }}</h3>
                <p>Nouveaux ce mois</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <h3 id="avg-orders">{{ number_format($avgOrdersPerClient, 1) }}</h3>
                <p>Commandes/client</p>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row filter-row">
        <div class="col-md-3">
            <label for="city-filter"><i class="fas fa-map-marker-alt"></i> Ville:</label>
            <select id="city-filter" class="form-control">
                <option value="">Toutes les villes</option>
                @foreach($cities as $city)
                    <option value="{{ $city }}">{{ $city }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="delegation-filter"><i class="fas fa-building"></i> Délégation:</label>
            <select id="delegation-filter" class="form-control">
                <option value="">Toutes les délégations</option>
                @foreach($delegations as $delegation)
                    <option value="{{ $delegation }}">{{ $delegation }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="orders-filter"><i class="fas fa-shopping-cart"></i> Nombre de commandes:</label>
            <select id="orders-filter" class="form-control">
                <option value="">Tous</option>
                <!--<option value="0">Aucune commande</option>-->
                <option value="1-5">1-5 commandes</option>
                <option value="6-10">6-10 commandes</option>
                <option value="10+">Plus de 10</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="date-filter"><i class="fas fa-calendar"></i> Période d'inscription:</label>
            <div class="d-flex gap-2">
                <input type="date" id="date_from" class="form-control" placeholder="Du">
                <input type="date" id="date_to" class="form-control" placeholder="Au">
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover" id="clients-table">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th>Client</th>
                        <th>Téléphones</th>
                        <th>Localisation</th>
                        <th>Commandes</th>
                        <th>Inscription</th>
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
<script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.3.0/js/responsive.bootstrap5.min.js"></script>
<script>
$(function() {
    let table = $('#clients-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('clients.getClients') }}",
            data: function(d) {
                d.city = $('#city-filter').val();
                d.delegation = $('#delegation-filter').val();
                d.orders_filter = $('#orders-filter').val();
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
            }
        },
        columns: [
            { data: 'client_info', name: 'client_info', orderable: false },
            { data: 'phones', name: 'phones', orderable: false },
            { data: 'location', name: 'location', orderable: false },
            { data: 'orders_stats', name: 'orders_stats', orderable: false },
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[4, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
        },
        pageLength: 25,
        responsive: true,
        drawCallback: function() {
            // Animation pour les nouvelles lignes
            $('#clients-table tbody tr').each(function() {
                $(this).fadeIn();
            });
        }
    });

    // Appliquer les filtres
    $('#city-filter, #delegation-filter, #orders-filter, #date_from, #date_to').on('change', function() {
        table.draw();
        updateStats();
    });

    // Fonction pour mettre à jour les statistiques
    function updateStats() {
        $.ajax({
            url: "{{ route('clients.getStats') }}",
            data: {
                city: $('#city-filter').val(),
                delegation: $('#delegation-filter').val(),
                orders_filter: $('#orders-filter').val(),
                date_from: $('#date_from').val(),
                date_to: $('#date_to').val()
            },
            success: function(data) {
                $('#total-clients').text(data.totalClients);
                $('#active-clients').text(data.activeClients);
                $('#new-clients').text(data.newClientsThisMonth);
                $('#avg-orders').text(data.avgOrdersPerClient);
            }
        });
    }

    // Animation au chargement
    $('.stats-card').each(function(index) {
        $(this).delay(index * 100).fadeIn();
    });
});
</script>
@endsection