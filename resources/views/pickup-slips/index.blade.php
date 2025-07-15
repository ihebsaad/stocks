@extends('layouts.admin')

@section('title', 'Bons de ramassage')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    .badge {
        font-size: 0.85em;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .dataTables_wrapper .dataTables_filter {
        float: right;
        text-align: right;
    }
    
    .dataTables_wrapper .dataTables_length {
        float: left;
    }
    
    .table-actions {
        white-space: nowrap;
    }
    
    .status-badge {
        min-width: 80px;
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="float-left">
            <h2>Bons de ramassage</h2>
        </div>
        <div class="float-right">
            <a class="btn btn-success" href="{{ route('pickup.create') }}">
                <i class="fas fa-plus"></i> Nouveau bon de ramassage
            </a>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $message }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if ($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $message }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card mt-3">
    <div class="card-header">
        <h4>Liste des bons de ramassage</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="pickup-slips-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Référence</th>
                        <th>Société de livraison</th>
                        <th>Nombre de colis</th>
                        <th>Statut</th>
                        <th>Créé par</th>
                        <th>Créé le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce bon de ramassage ?</p>
                <p><strong>Référence:</strong> <span id="delete-reference"></span></p>
                <p class="text-danger"><small>Cette action est irréversible.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    // Configuration du DataTable
    const table = $('#pickup-slips-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ route("pickup.data") }}',
        columns: [
            { data: 'date', name: 'date' },
            { data: 'reference', name: 'reference' },
            { data: 'delivery_company', name: 'delivery_company.name' },
            { data: 'parcels_count', name: 'parcels_count', orderable: false, searchable: false },
            { data: 'status', name: 'status' },
            { data: 'user', name: 'user.name' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[6, 'desc']], // Trier par date de création (descendant)
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            processing: "Traitement en cours...",
            search: "Rechercher&nbsp;:",
            lengthMenu: "Afficher _MENU_ &nbsp;éléments",
            info: "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            infoEmpty: "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
            infoFiltered: "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            loadingRecords: "Chargement en cours...",
            zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
            emptyTable: "Aucune donnée disponible dans le tableau",
            paginate: {
                first: "Premier",
                previous: "Pr&eacute;c&eacute;dent",
                next: "Suivant",
                last: "Dernier"
            },
            aria: {
                sortAscending: ": activer pour trier la colonne par ordre croissant",
                sortDescending: ": activer pour trier la colonne par ordre décroissant"
            }
        }
    });

    // Gestionnaire pour le modal de suppression
    $('#pickup-slips-table').on('click', '.delete-btn', function() {
        const pickupSlipId = $(this).data('id');
        const reference = $(this).data('reference');
        
        $('#delete-reference').text(reference);
        $('#delete-form').attr('action', '/pickup/' + pickupSlipId);
        $('#deleteModal').modal('show');
    });

    // Actualiser le tableau après suppression
    $('#delete-form').submit(function(e) {
        e.preventDefault();
        
        const form = $(this);
        const url = form.attr('action');
        
        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                $('#deleteModal').modal('hide');
                table.ajax.reload();
                
                // Afficher un message de succès
                $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                  'Bon de ramassage supprimé avec succès' +
                  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                  '<span aria-hidden="true">&times;</span></button></div>')
                  .insertAfter('.row:first')
                  .delay(5000)
                  .fadeOut();
            },
            error: function(xhr) {
                $('#deleteModal').modal('hide');
                
                // Afficher un message d'erreur
                const errorMessage = xhr.responseJSON?.message || 'Erreur lors de la suppression';
                $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                  errorMessage +
                  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                  '<span aria-hidden="true">&times;</span></button></div>')
                  .insertAfter('.row:first')
                  .delay(5000)
                  .fadeOut();
            }
        });
    });

    // Auto-refresh toutes les 30 secondes
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 30000);
});
</script>
@endsection