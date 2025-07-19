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
                <h2>Liste des produits</h2>
            </div>
            <div class="float-right mb-3"  >
                <a class="btn btn-success" href="{{ route('products.create') }}"><i class="fas fa-plus"></i> Ajouter un produit</a>
            </div>
        </div>
    </div>
    <!--
    <div class="row mb-3">
        <div class="col-md-3">
            <input type="text" id="filter-reference" class="form-control" placeholder="Filtrer par référence">
        </div>
        <div class="col-md-3">
            <input type="text" id="filter-name" class="form-control" placeholder="Filtrer par nom">
        </div>
        <div class="col-md-3">
            <input type="text" id="filter-provider" class="form-control" placeholder="Filtrer par fournisseur">
        </div>
        <div class="col-md-3">
            <input type="text" id="filter-category" class="form-control" placeholder="Filtrer par catégorie">
        </div>
    </div>
    -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id='mytable'>
            <thead>
                <tr>
                <th>Reference</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Fournisseur</th>
                <th>Type</th>
                <th>Prix</th>
                <th>Qté</th>
                <th>Description</th>
                <th class="no-sort"  style="width:20%"  >Action</th>
                </tr>
            </thead>
        </table>
    </div>

@endsection
@section('footer-scripts')

<!-- DataTables  & Plugins -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/jszip/jszip.min.js')}}"></script>
<script src="{{asset('plugins/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{asset('plugins/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>


<script>
  

    $(function() {
        var table = $('#mytable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route("products.list") }}',
                type: 'GET',
                data: function (d) {
                    // Ajoutez ici des paramètres supplémentaires si nécessaire
                }
            },
            columns: [
                { data: 'reference', name: 'reference' },
                { data: 'name', name: 'name' },
                { 
                    data: 'category_name', 
                    name: 'categorie.name', // Important pour le filtrage côté serveur
                    orderable: true,
                    searchable: true
                },
                { 
                    data: 'provider_name', 
                    name: 'provider.company', // Important pour le filtrage côté serveur
                    orderable: true,
                    searchable: true
                },
                { 
                    data: 'type', 
                    name: 'type', // Important pour le filtrage côté serveur
                    orderable: false,
                    searchable: false
                },
                { data: 'prix_ttc', name: 'prix_ttc', orderable: true, searchable: true },
                { data: 'stock_quantity', name: 'stock_quantity', orderable: true, searchable: true },
                { 
                    data: 'description', 
                    name: 'description',
                    orderable: false,
                    searchable: true
                },
                { 
                    data: 'action', 
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            order: [[0, 'asc']],
            initComplete: function() {
                // Initialisation des filtres individuels si nécessaire
                this.api().columns().every(function() {
                    var column = this;
                    if (column.header().getAttribute('data-filter') === 'true') {
                        var input = document.createElement('input');
                        $(input).appendTo($(column.header()).empty())
                                .on('keyup change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                });
                    }
                });
            }
        });

        $('#mytable').on('draw.dt', function() {
            var table = $(this).DataTable();
            
            // Cibler toutes les cellules de la colonne "stock_quantity"
            table.column('stock_quantity:name').nodes().each(function(cell, index) {
                var rowData = table.row($(cell).parent()).data(); // Données de la ligne
                var qty = parseFloat($(cell).text());
                var minQty = parseFloat(rowData.min_qty); // Supposant que min_qty est dans les données
                
                $(cell).removeClass('bg-danger bg-warning'); // Réinitialiser
                
                if (minQty > 0 && minQty >= qty) {
                    $(cell).addClass('bg-danger');
                } else if (minQty > 0 && (qty - minQty) < 6) {
                    $(cell).addClass('bg-warning');
                }
            });
        });  
 
    });
</script>
 
@endsection
