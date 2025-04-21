@extends('layouts.admin')
  
@section('content')
<div class="row pl-3">
    <div class="col-lg-12 margin-tb">
        <div class="float-left">
            <h2>Ajouter une catégorie</h2>
        </div>
        <div class="float-right">
            <a class="btn btn-primary" href="{{ route('categories.index') }}"> Retour</a>
        </div>
    </div>
</div>
      
<form action="{{ route('categories.store') }}" method="POST">
    @csrf
     <div class="row pl-3">
        <div class="col-xs-12 col-sm-12 col-md-7">
            <div class="form-group">
                <strong>Nom:</strong>
                <input type="text" name="name" class="form-control" placeholder="Nom" value="{{old('name')}}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-7">
            <div class="form-group">
                <strong>Description:</strong>
                <textarea class="form-control summernote" style="height:150px" name="description" placeholder="Description">{{old('description')}}</textarea>
            </div>
        </div>
			
        <div class="col-xs-12 col-sm-12 col-md-7">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </div>
   
</form>
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