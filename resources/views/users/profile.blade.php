@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dropzone.css') }}" />
<!--  datepicker  -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">

@endsection

@section('content')
  <div class="row">
    <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                   @if(Auth::user()->thumb!='')
                  <img class="profile-user-img img-fluid img-circle" src="<?php echo URL::asset('img/users/'.$User->thumb);?>" alt="User profile picture">
                   @else
                  <img class="profile-user-img img-fluid img-circle" src="{{ asset('img/users/user.png')}}" width="160"   alt="User Image">
                  @endif
                </div>

                <h3 class="profile-username text-center">{{$User->name}} {{$User->lastname}} </h3>

                <p class="text-muted text-center">{{$User->entreprise}}</p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Compte</b> <a class="float-right">{{$User->user_type}}</a>
                  </li>
                  <li class="list-group-item">
                    <b>Inscription</b> <a class="float-right">{!! date('d/m/Y', strtotime($User->created_at)) !!}</a>
                  </li>
 
                </ul>

                <!--<a href="#" class="btn btn-primary btn-block"><b>Analyse Profiler</b></a>-->
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Me Box -->
            <div class="card card-primary">
				<div class="card-header">
					<h3 class="card-title">Changer mon image</h3>
				</div>
              <!-- /.card-header -->
				<div class="card-body">
					<form  action="{{ route('users.ajoutimage') }}" class="dropzone"   id="dropzoneFrom">
						{{ csrf_field() }}
						<input type="hidden" name="user"  value="<?php echo $user->id; ?>">
					</form>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
    </div>
    <div class="col-md-6">
      <div class="card card-primary card-outline">
        <div class="card-body box-profile">
			<form class="user"   method="post" action="{{ route('updateuser') }}"    >
				<input type="hidden" value="{{$id}}" id="iduser" name="user">
                {{ csrf_field() }}
				<div class="form-group row">
                    <div class="col-sm-6 mb-3 mb-sm-0">
						<label>Prénom</label>
                        <input type="text" class="form-control form-control-user" id="name" name="name"  value="{{ $user->name }}"  placeholder="Prénom">

                    </div>
                    <div class="col-sm-6">
						<label>Nom</label>											
                        <input type="text" class="form-control form-control-user" id="lastname" name="lastname"  value="{{ $user->lastname }}"  placeholder="Nom">
                    </div>

                </div>
   
                <div class="form-group row">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <label>Adresse email </label>											
                        <input type="email" class="form-control form-control-user" id="email" name="email"  readonly value="{{ $user->email }}" placeholder="Adresse email"/>
                                         							 
                    </div>
                    <div class="col-sm-6 mb-3 mb-sm-0">
	 
				
                    </div>
                </div>

                    <!-- <div class="form-group row">
                    <div class="col-sm-6 mb-3 mb-sm-0">
					<label>Date de naissance</label>											
                    <input type="text" class="form-control form-control-user" id="naissance" name="naissance"  value="{{ $user->naissance  }}"   placeholder="">
                    </div>
                    <div class="col-sm-6 mb-3 mb-sm-0">
					<label>Adresse</label>											
                    <textarea   class="form-control form-control-user" id="adresse" name="adresse" >{{ $user->adresse  }}</textarea>                                                 
                    </div>
                    </div>-->

                    <div class="form-group row">
                        <div class="col-sm-6 mb-3 mb-sm-0">
							<label>Mot de passe</label>											
                            <input type="password" class="form-control form-control-user" name="password" pattern=".{6,30}"  style="width:100%"  autocomplete="off" id="password" placeholder="Mot de passe">
						</div>
						<div class="col-sm-6 mb-3 mb-sm-0">
							<label>Confirmation</label>											
                            <input type="password" class="form-control form-control-user" name="confirmation"  pattern=".{6,30}" style="width:100%"  autocomplete="off" id="confirmation" placeholder="Confirmation">
										 
                        </div>
                    </div>
										
										
                    <div class="form-group row  ml-3 mt-5">
						<button value="update"  name="update"   type="submit"  class="  btn btn-success btn-icon-split  ml-20   mt-50 mb-30">
                            <span class="icon text-white-50">
                                <i class="fas fa-save"></i>
                            </span>
                            <span class="text " style="width:120px" >    Modifier</span>
                        </button>
                    </div>

          </form>
         </div>
        </div>
	</div>


</div>					

@endsection

@section('footer-scripts')
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
  <script>/*
  $( function() {
    $( "#naissance" ).datepicker(
        {
      changeMonth: true,
      changeYear: true,
  
    }
    );

    $( "#naissance" ).datepicker( "option", "dateFormat", "dd/mm/yy" );
  } );*/
  </script>

<script src="{{ asset('js/dropzone.js') }}" ></script>
<script>
  Dropzone.options.dropzoneFrom = {
 // autoProcessQueue: false,
  acceptedFiles:".png,.jpg,.gif,.bmp,.jpeg",
  dictDefaultMessage: 'Glissez votre image ici',

  init: function(){
 
   this.on("complete", function(){
  
  location.reload();
 });
  },
 };
 </script>
@endsection
