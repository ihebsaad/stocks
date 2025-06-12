@extends('layouts.admin')


@section('content')
<link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">

<div class="row pl-3">
    <div class="col-lg-12 margin-tb">
        <div class="float-left">
            <h2>Ajouter un client</h2>
        </div>
        <div class="float-right">
            <a class="btn btn-primary" href="{{ route('customers.index') }}"> Retour</a>
        </div>
    </div>
</div>

<form action="{{ route('customers.store') }}" method="POST">
    @csrf
    <!--
        <div class="row pl-3">

            <div class="col-xs-12 col-sm-6 col-md-6">
                <div class="form-group">
                    <strong>Commercial*:</strong>
                    <select class="form-control select2" name="commercial"  >
                        <option></option>
                        @foreach($commercials as $commercial)
                            <option  value="{{$commercial->id}}">{{$commercial->id}} : {{$commercial->name}} {{$commercial->lastname}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>
-->
    <div class="row pl-3">
        <div class="col-xs-12 col-sm-12 col-md-1">
            <div class="form-group">
                <strong>Civilité:</strong>
                <select name="civility" class="form-control" placeholder="civility" required>
                    <option value="Mr">Mr</option>
                    <option value="Mme">Mme</option>
                    <option value="Mlle">Mlle</option>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="form-group">
                <strong>Nom:</strong>
                <input type="text" name="lastname" class="form-control" placeholder="Nom" value="{{old('lastname')}}" required>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="form-group">
                <strong>Prénom:</strong>
                <input type="text" name="name" class="form-control" placeholder="Prénom" value="{{old('name')}}" required>
            </div>
        </div>


    </div>

    <div class="row pl-3">
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="form-group">
                <strong>Société:</strong>
                <input type="text" name="company" class="form-control" placeholder="Nom de la société" value="{{old('company')}}">
            </div>
        </div>
    </div>
    <div class="row pl-3">
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="form-group">
                <strong>Tél1:</strong>
                <input type="text" name="phone" class="form-control" placeholder="N° de téléphone" value="{{old('phone')}}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="form-group">
                <strong>Tél2:</strong>
                <input type="text" name="phone2" class="form-control" placeholder="N° de téléphone 2" value="{{old('phone2')}}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="form-group">
                <strong>Email:</strong>
                <input type="email" name="email" class="form-control" placeholder="adresse email" value="{{old('email')}}">
            </div>
        </div>
    </div>

    <div class="row pl-3 mt-2">

        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Adresse:</strong>
                <input type="text" name="address" id="address" class="form-control" placeholder="Adresse" value="{{old('address')}}" onchange="copy('address')" />
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="form-group">
                <strong>Ascenceur:</strong>
                <select name="ascenceur" id="ascenceur" class="form-control" style="max-width:100px">
                    <option value="Non">Non</option>
                    <option value="Oui">Oui</option>
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="form-group">
                <strong>Étage:</strong>
                <input type="text" name="etage" id="etage" class="form-control" placeholder="N°" value="" style="max-width:100px">
            </div>
        </div>
    </div>


    <div class="row pl-3">

        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="form-group">
                <input type="text" name="city" id="city" class="form-control" placeholder="Ville" value="{{old('city')}}" onchange="copy('city')">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="form-group">
                <select type="text" name="country" id="country" class="form-control" onchange="copy('country')">
                    <option></option>
                    @foreach($countries as $key =>$value)
                    <option value="{{$value}}" @if($value=='France' ) selected="selected" @endif>{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-2">
            <div class="form-group">
                <input type="text" name="postal" id="postal" class="form-control" placeholder="Code postal" value="{{old('postal')}}" onchange="copy('postal')">
            </div>
        </div>

    </div>

    <div class="col-xs-12 col-sm-12 col-md-6">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
    </div>

</form>
@endsection


@section('footer-scripts')
<script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>

<script>
    $(function() {
        // Summernote
        $('.summernote').summernote();

        $('.select2').select2();

    });


    function copy(champ) {
        val = $('#' + champ).val();
        if ($('#delivery_' + champ).val() == '' || $('#delivery_' + champ).val() == 'France') {
            $('#delivery_' + champ).val(val);
        }
    }
</script>

<!--
<script src="https://cdn.jsdelivr.net/npm/places.js@1.19.0"></script>
-->
<script>
    /*
(function() {
  var placesAutocomplete = places({
    appId: "{{env('algolia_appid','EXL1N4GLVM')}}" ,
    apiKey: "{{env('algolia_apiKey','731b455dad1acd46fbc8a378489358de')}}" ,
    container: document.querySelector('#address'),
    templates: {
      value: function(suggestion) {
        return suggestion.name;
      }
    }
  }).configure({
    type: 'address',
    language: 'fr'
  });
  placesAutocomplete.on('change', function resultSelected(e) {
    document.querySelector('#city').value = e.suggestion.city || '';
    document.querySelector('#postal').value = e.suggestion.postcode || '';
  });
})();
*/
</script>
@endsection