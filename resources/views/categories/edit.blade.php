@extends('layouts.admin')
@section('styles')

  <!-- summernote -->
  <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css')}}">
<style>

</style>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="float-left">
                <h2>Catégorie {{$categorie->name }}</h2>
            </div>
            <div class="float-right">
                <a class="btn btn-primary" href="{{ route('categories.index') }}"> Retour</a>
            </div>
        </div>
    </div>
   
  
    <div class="row">
		<div class="col-lg-7">
      <form action="{{ route('categories.update',$categorie->id) }}" method="POST">
				@csrf
				@method('PUT')
		   
				 <div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group">
							<strong>Nom:</strong>
							<input type="text" name="name" value="{{ $categorie->name }}" class="form-control" placeholder="name">
						</div>
					</div>
		
					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group">
							<strong>Description:</strong>
							<textarea class="form-control summernote" style="height:150px" name="description" placeholder="Description">{{ $categorie->description }}</textarea>
						</div>
					</div>
		

							

					<div class="col-xs-12 col-sm-12 col-md-12 text-center ">
					  <button type="submit" class="btn btn-primary float-right mb-3">Enregistrer</button>
					</div>
				</div>
		   
			</form>
		</div>

		<div class="col-lg-5">

		</div>
	</div>
@endsection


@section('footer-scripts')
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}" ></script>

<script>

 
  $(function () {
    // Summernote
    $('.summernote').summernote()
  });
  
</script>
 
@endsection

