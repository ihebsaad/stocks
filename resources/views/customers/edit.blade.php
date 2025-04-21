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
            <h2>Client {{$customer->name}} {{$customer->lastname}} </h2>
        </div>
        <div class="float-right">
            <a class="btn btn-primary" href="{{ route('customers.index') }}"> Retour</a>
        </div>
    </div>
</div>

<form action="{{ route('customers.update',$customer->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row pl-3">
        <div class="col-xs-12 col-sm-12 col-md-1">
            <div class="form-group">
                <strong>Civilité:</strong>
                <select name="civility" class="form-control" placeholder="civility">
                    <option @if($customer->civility=='Mr') selected="selected" @endif value="Mr">Mr</option>
                    <option @if($customer->civility=='Mme') selected="selected" @endif value="Mme">Mme</option>
                    <option @if($customer->civility=='Mlle') selected="selected" @endif value="Mlle">Mlle</option>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="form-group">
                <strong>Nom:</strong>
                <input type="text" name="lastname" class="form-control" placeholder="Nom" value="{{$customer->lastname}}">
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="form-group">
                <strong>Prénom:</strong>
                <input type="text" name="name" class="form-control" placeholder="Prénom" value="{{$customer->name}}">
            </div>
        </div>
    </div>

    <div class="row pl-3">
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="form-group">
                <strong>Société:</strong>
                <input type="text" name="company" class="form-control" placeholder="Nom de la société" value="{{$customer->company}}">
            </div>
        </div>
    </div>
    <div class="row pl-3">
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="form-group">
                <strong>Tél:</strong>
                <input type="text" name="phone" class="form-control" placeholder="N° de téléphone" value="{{$customer->phone}}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="form-group">
                <strong>Tél2:</strong>
                <input type="text" name="phone2" class="form-control" placeholder="N° de téléphone 2" value="{{$customer->phone2}}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="form-group">
                <strong>Email:</strong>
                <input type="email" name="email" class="form-control" placeholder="adresse email" value="{{$customer->email}}">
            </div>
        </div>
    </div>

    <div class="row pl-3 mt-2">

        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Adresse:</strong>
                <input type="text" name="address" id="address" class="form-control" placeholder="Adresse" value="{{$customer->address}}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="form-group">
                <strong>Ascenceur:</strong>
                <select name="ascenceur" id="ascenceur" class="form-control" style="max-width:100px">
                    <option  @if($customer->ascenceur=='Non') selected="selected" @endif value="Non">Non</option>
                    <option  @if($customer->ascenceur=='Oui') selected="selected" @endif value="Oui">Oui</option>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="form-group">
                <strong>Étage:</strong>
                <input type="text" name="etage" id="etage" class="form-control" placeholder="N°" value="{{$customer->etage}}" style="max-width:100px">
            </div>
        </div>

    </div>


    <div class="row pl-3">

        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="form-group">
                <input type="text" name="city" id="city" class="form-control" placeholder="Ville" value="{{$customer->city}}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="form-group">
                <select type="text" name="country" id="country" class="form-control">
                    <option></option>
                    @foreach($countries as $key =>$value)
                    <option value="{{$value}}" @if($value==$customer->country) selected="selected" @endif >{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-2">
            <div class="form-group">
                <input type="text" name="postal" id="postal" class="form-control" placeholder="Code postal" value="{{$customer->postal}}">
            </div>
        </div>

    </div>

    <div class="col-xs-12 col-sm-12 col-md-6">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>

</form>

@endsection


@section('footer-scripts')
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>

<script>
    $(function() {
        // Summernote
        $('.summernote').summernote()
    });
</script>

@endsection