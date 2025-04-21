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
                <h2>Liste des utilisateurs</h2>
            </div>
            @if(auth()->user()->id==1 || auth()->user()->id==4 )
            <div class="float-right mb-3"  >
                <a class="btn btn-success" href="{{ route('users.create') }}"><i class="fas fa-plus"></i> Ajouter un utilisateur</a>
            </div>
            @endif
        </div>
    </div>

   <style>
		.small-img{width:150px;}
   </style>
    <table class="table table-bordered table-striped" id='mytable'>
        <thead>
            <tr>
            <th>No</th>
            <th class="no-sort">Image</th>
            <th>Prénom</th>
            <th>Nom</th>
            <th>Email</th>
            <th class="no-sort" style="width:20%"  >Action</th>
            </tr>
        </thead>
        @foreach ($users as $user)
		<?php
        if($user->thumb!='')
        $url_img= asset('img/users/'.$user->thumb);
        else
        $url_img= asset('img/users/user.png');
        ?>
        <tr>
            <td>{!! sprintf('%04d',$user->id) !!}</td>
            <td><img  width="100" src="{!! $url_img !!}"/></td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->lastname }}</td>
            <td>{{ $user->email }}</td>
            <td>
                @if(auth()->user()->user_type=='admin' )
			    <a class="btn btn-primary mb-3" href="{{ route('users.edit',$user->id) }}" style="float:left" title="Modifier"><i class="fas fa-edit"></i></a>
			<!--	    @if($user->status)
                    <a title="Désactiver"   href="{{route('desactiver', ['id'=>$user->id] )}}" class="btn btn-danger mb-3" role="button" data-toggle="tooltip" data-tooltip="tooltip" data-placement="bottom" data-original-title="Désactiver" style="float:left">
                        <span class="fas fa-user-times"></span> Désactiver
                    </a>
                    @else
                    <a title="Activer"   href="{{route('activer', $user->id )}}" class="btn btn-success mb-3" role="button" data-toggle="tooltip" data-tooltip="tooltip" data-placement="bottom" data-original-title="Activer" style="float:left" >
                        <span class="fas fa-user-check  "></span> Activer
                    </a>
                    @endif
-->
                    <form action="{{ route('users.destroy',$user->id) }}" method="POST" style="float:left" class="mr-2 ml-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger mb-3" title="Supprimer"><i class="fas fa-trash"></i></button>
                    </form>
                    <!--<a title="se connecter en tant que"   href="{{route('loginAs', $user->id )}}" class="btn btn-secondary mb-3" role="button" data-toggle="tooltip" data-tooltip="tooltip" data-placement="bottom" data-original-title="Connexion" >
                                <span class="fas fa-sign-out-alt  "></span> Se connecter
                    </a>-->
				@endif
            </td>
        </tr>
        @endforeach
    </table>


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
  $(function () {
    $("#mytable").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      buttons: [
                    {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i>  Imprimer',
                    exportOptions: {
                    columns: [ 0,1,2,3,4  ],
                	}
                    },
                    {
                    extend: 'csv',
                    text: '<i class="fa fa-file-csv"></i>  Csv',
                    exportOptions: {
                    columns: [ 0,1,2,3,4  ],
                	}
                    },
				 {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel"></i>  Excel',
                    exportOptions: {
                    columns: [ 0,1,2,3,4]
               	}
                    },
				{
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf"></i>  Pdf',
                    exportOptions: {
                    columns: [  0,1,2,3,4]
                	}
                    }

                ]   ,   "language": {
					"decimal":        "",
					"emptyTable":     "Pas de données",
					"info":           "Affichage de  _START_ à _END_ de _TOTAL_ entrées",
					"infoEmpty":      "Affichage 0 à 0 of 0 entries",
					"infoFiltered":   "(filteré de _MAX_ total entrées)",
					"infoPostFix":    "",
					"thousands":      ",",
					"lengthMenu":     "afficher _MENU_ ",
					"loadingRecords": "Chargement...",
					"processing":     "Chargement...",
					"search":         "Recherche:",
					"zeroRecords":    "Pas de résultats",
						"paginate": {
						"first":      "Premier",
						"last":       "Dernier",
						"next":       "Suivant",
						"previous":   "Premier"
									},
						"aria": {
						"sortAscending":  ": Activer pour un Tri ascendant",
						"sortDescending": ": Activer pour un Tri descendant"
								}
					},
                    "columnDefs": [ {
                    "targets": 'no-sort',
                    "orderable": false,
                        } ],
    }).buttons().container().appendTo('#mytable_wrapper .col-md-6:eq(0)');


  });
</script>

@endsection
