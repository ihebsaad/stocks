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
    .status-no_stock { background-color: #a82b89; color: white; }
    .status-rendezvous { background-color: #fa7e9d; color: white; }
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
    

    
    .selection-info {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 10px;
    }
    
    #generate-pdf-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
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
    
    <!-- Selection Actions -->
    <div class="selection-actions">
        <div class="row align-items-center">
            <div class="col-md-3">
                <div class="selection-info">
                    <span id="selected-count">0</span> colis sélectionné(s)
                </div>
                <div class="btn-group" role="group">
                    <button type="button" id="select-all-btn" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-check-square"></i> Tout sélectionner
                    </button>
                    <button type="button" id="deselect-all-btn" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-square"></i> Tout désélectionner
                    </button>
                </div>
            </div>
            <div class="col-md-3 text-end">
                <button type="button" id="generate-pdf-btn" class="btn btn-success" disabled>
                    <i class="fas fa-file-pdf"></i> Générer PDF
                </button>
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
                <label for="user-filter">Date:</label><br>
                De: <input type="date" name="date_from" id="date_from" class="form-control" placeholder="Date de début" format="YYYY-MM-DD" style="width:150px;display:inline-block;" > 
                A: <input type="date" name="date_to" id="date_to" class="form-control" placeholder="Date de fin" format="YYYY-MM-DD" style="width:150px;display:inline-block;">
            </div>
        </div>
    </div>
     
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="parcels-table">
            <thead>
                <tr>
                    <th width="50px">
                        <input type="checkbox" id="select-all-checkbox">
                    </th>
                    <th>Référence</th>
                    <th>Date de création</th>
                    <th>Client</th>
                    <th>Statut</th>
                    <th>Livraison</th>
                    <th>Commande</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-2">Génération du PDF en cours...</p>
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
    let table = $('#parcels-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        type: 'GET',
        dataType: "json", 
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        ajax: {
            url: "{{ route('parcels.getParcels') }}",
            data: function(d) {
                d.delivery_company = $('#delivery-company-filter').val();
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
            },
        },
        columns: [
            { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
            { data: 'reference', name: 'reference' },
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'client', name: 'client', orderable: false, searchable: false },
            { data: 'dernier_etat', name: 'dernier_etat' },
            { data: 'delivery_company', name: 'delivery_company' },
            { data: 'order_id', name: 'order_id' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
        },
        drawCallback: function() {
            updateSelectionCount();
        }
    });

    // Handle individual checkbox changes
    $(document).on('change', '.parcel-checkbox', function() {
        updateSelectionCount();
        updateSelectAllCheckbox();
    });

    // Handle select all checkbox in header
    $('#select-all-checkbox').on('change', function() {
        let isChecked = $(this).is(':checked');
        $('.parcel-checkbox:visible').prop('checked', isChecked);
        updateSelectionCount();
    });

    // Handle select all button
    $('#select-all-btn').on('click', function() {
        $('.parcel-checkbox:visible').prop('checked', true);
        $('#select-all-checkbox').prop('checked', true);
        updateSelectionCount();
    });

    // Handle deselect all button
    $('#deselect-all-btn').on('click', function() {
        $('.parcel-checkbox').prop('checked', false);
        $('#select-all-checkbox').prop('checked', false);
        updateSelectionCount();
    });

    // Handle generate PDF button
    $('#generate-pdf-btn').on('click', function() {
        let selectedIds = [];
        $('.parcel-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert('Veuillez sélectionner au moins un colis.');
            return;
        }

        // Show loading modal
        $('#loadingModal').modal('show');

        // Create form and submit
        let form = $('<form>', {
            'method': 'POST',
            'action': '{{ route("parcels.generatePdf") }}',
            'target': '_blank'
        });

        // Add CSRF token
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': '{{ csrf_token() }}'
        }));

        // Add selected IDs
        selectedIds.forEach(function(id) {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'parcel_ids[]',
                'value': id
            }));
        });

        // Submit form
        $('body').append(form);
        form.submit();
        form.remove();

        // Hide loading modal after a delay
        setTimeout(function() {
            $('#loadingModal').modal('hide');
        }, 2000);
    });

    function updateSelectionCount() {
        let count = $('.parcel-checkbox:checked').length;
        $('#selected-count').text(count);
        $('#generate-pdf-btn').prop('disabled', count === 0);
    }

    function updateSelectAllCheckbox() {
        let totalVisible = $('.parcel-checkbox:visible').length;
        let totalChecked = $('.parcel-checkbox:visible:checked').length;
        
        if (totalChecked === 0) {
            $('#select-all-checkbox').prop('indeterminate', false);
            $('#select-all-checkbox').prop('checked', false);
        } else if (totalChecked === totalVisible) {
            $('#select-all-checkbox').prop('indeterminate', false);
            $('#select-all-checkbox').prop('checked', true);
        } else {
            $('#select-all-checkbox').prop('indeterminate', true);
        }
    }

    $('#delivery-company-filter , #date_from , #date_to').on('change', function() {
        // Redraw the table with new filters
        table.draw();
    });
});
</script>
@endsection