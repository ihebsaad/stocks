@extends('layouts.admin')
@section('styles')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.css" />
  <!-- summernote -->
  <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css')}}">
<style>

</style>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="float-left">
                <h2>Utilisateur {{$user->name}} {{$user->lastname}}</h2>
            </div>
            <div class="float-right">
                <a class="btn btn-primary" href="{{ route('users.index') }}"> Retour</a>
            </div>
        </div>
    </div>


    <div class="row">
		<div class="col-lg-7">

			<form action="{{ route('users.update',$user->id) }}" method="POST">
				@csrf
				@method('PUT')

				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-6">
						<div class="form-group">
							<strong>Nom:</strong>
							<input type="text" name="lastname" value="{{ $user->lastname }}" class="form-control" placeholder="lastname">
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-6">
						<div class="form-group">
							<strong>Prénom:</strong>
							<input type="text" name="name" value="{{ $user->name }}" class="form-control" placeholder="name">
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-6">
						<div class="form-group">
							<strong>Email:</strong>
							<input type="email" name="email" value="{{ $user->email }}" class="form-control" placeholder="Email">
						</div>
					</div>
					<div class="col-xs-12 col-sm-12 col-md-6">
						<div class="form-group">
							<strong>Accès:</strong>
							<select class="form-control" name="user_type">
								<option  @if( $user->user_type=="user") selected="selected"  @endif value="user">Accès commercial</option>
								<option  @if( $user->user_type=="admin")  selected="selected"  @endif value="admin">Accès complet</option>
							</select>
						</div>
					</div>

				</div>

				<div class="row mt-3">
					<div class="col-xs-12 col-sm-12 col-md-12 text-center ">
					  <button type="submit" class="btn btn-primary float-right mb-3">Enregistrer</button>
					</div>
				</div>

			</form>
		</div>

		<div class="col-lg-5">
			<div class="form-group">
						<strong>Image:</strong>
						<form  action="{{ route('users.ajoutimage') }}" class="dropzone"   id="dropzoneFrom">
						 {{ csrf_field() }}
						<input type="hidden" name="user"  value="<?php echo $user->id; ?>">
						</form>
						@if($user->thumb!='')
							<img id='img' src="<?php echo  URL::asset('/img/users/'.$user->thumb);?>" style="max-width:250px;margin:20px 20px 20px 20px">
						@else
							<img id='img' style='display:none' />
						@endif
			</div>
		</div>
	</div>
@endsection



<!--  https://www.webslesson.info/2018/07/dropzonejs-with-php-for-upload-file.html-->

@section('footer-scripts')
<script src="{{ asset('js/dropzone.js') }}" ></script>
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}" ></script>

<script>
  Dropzone.options.dropzoneFrom = {
 // autoProcessQueue: false,
  acceptedFiles:".png,.jpg,.gif,.bmp,.jpeg",
  dictDefaultMessage: 'Glissez votre image ici',

  init: function(){

   this.on("complete", function(){
  /*  if(this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0)
    {
     var _this = this;
     _this.removeAllFiles();
    }*/
  //  list_image();
	$('#img').hide();
 });
  },
 };

  $(function () {
    // Summernote
    $('.summernote').summernote()
  });

</script>

@endsection

