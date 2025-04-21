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
                <h2>Fournisseur {{$provider->company}}   </h2>
            </div>
            <div class="float-right">
                <a class="btn btn-primary" href="{{ route('providers.index') }}"> Retour</a>
            </div>
        </div>
    </div>

    <form action="{{ route('providers.update',$provider->id) }}" method="POST">
		@csrf
		@method('PUT')


 
        <div class="row pl-3">
            <div class="col-xs-12 col-sm-12 col-md-4">
                <div class="form-group">
                    <strong>Société:</strong>
                    <input type="text" name="company" class="form-control" placeholder="Nom de la société" value="{{$provider->company}}">
                </div>
            </div>
        </div>
        <div class="row pl-3">
            <div class="col-xs-12 col-sm-12 col-md-4">
                <div class="form-group">
                    <strong>Tél:</strong>
                    <input type="text" name="phone" class="form-control" placeholder="N° de téléphone" value="{{$provider->phone}}">
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-4">
                <div class="form-group">
                    <strong>Email:</strong>
                    <input type="email" name="email" class="form-control" placeholder="adresse email"  value="{{$provider->email}}">
                </div>
            </div>
        </div>

        <div class="row pl-3 mt-2">

            <div class="col-xs-12 col-sm-12 col-md-8">
                <div class="form-group">
                    <strong>Adresse:</strong>
                    <input type="text"   name="address" id="address" class="form-control" placeholder="Adresse"   value="{{$provider->address}}">
                </div>
            </div>
        </div>


        <div class="row pl-3">

            <div class="col-xs-12 col-sm-12 col-md-3">
                <div class="form-group">
                    <input type="text" name="city" id="city" class="form-control" placeholder="Ville" value="{{$provider->city}}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-3">
                <div class="form-group">
                    <select type="text" name="country" id="country" class="form-control" >
                        <option></option>
                        @foreach($countries as $key =>$value)
                            <option value="{{$value}}"  @if($value== $provider->country) selected="selected" @endif >{{$value}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-2">
                <div class="form-group">
                    <input type="text" name="postal" id="postal"  class="form-control" placeholder="Code postal"  value="{{$provider->postal}}" >
                </div>
            </div>

        </div>

		<div class="row pl-3 mt-2">
 
            <div class="col-xs-12 col-sm-12 col-md-4">
                <div class="form-group">
                    <strong>Nom de contact:</strong>
                    <input type="text" name="lastname" class="form-control" placeholder="Nom" value="{{$provider->lastname}}">
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-4">
                <div class="form-group">
                    <strong>Prénom de contact:</strong>
                    <input type="text" name="name" class="form-control" placeholder="Prénom" value="{{$provider->name}}">
                </div>
            </div>
        </div>
		
		
		<div class="row pl-3">
            <div class="col-xs-12 col-sm-12 col-md-4">
                <div class="form-group">
                    <strong>Tél de contact:</strong>
                    <input type="text" name="phone_contact" class="form-control" placeholder="N° de téléphone" value="{{$provider->phone_contact}}">
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-4">
                <div class="form-group">
                    <strong>Email de contact:</strong>
                    <input type="email" name="email_contact" class="form-control" placeholder="adresse email"  value="{{$provider->email_contact}}">
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-6">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
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

