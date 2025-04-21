@extends('layouts.admin')

@section('content')
<div class="row pl-3">
    <div class="col-lg-12 margin-tb">
        <div class="float-left">
            <h2>Ajouter un utilisateur</h2>
        </div>
        <div class="float-right">
            <a class="btn btn-primary" href="{{ route('users.index') }}"> Retour</a>
        </div>
    </div>
</div>

<form action="{{ route('users.store') }}" method="POST">
    @csrf

     <div class="row pl-3">

        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Prénom:</strong>
                <input type="text" name="name" class="form-control" placeholder="Prénom*" value="{{old('name')}}" required>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Nom:</strong>
                <input type="text" name="lastname" class="form-control" placeholder="Nom" value="{{old('lastname')}}">
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Email:</strong>
                <input type="email" class="form-control form-control-user" id="email" name="email"  required   Autocomplete="NoAutocomplete"       value="{{old('email')}}"  placeholder="Adresse Email*"   oninvalid="this.setCustomValidity('Insérez une adresse email valide')"   oninput="this.setCustomValidity('')">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="form-group">
                <strong>Accès:</strong>

                <select class="form-control" name="user_type">
                    <option selected="selected" value="user">Accès commercial</option>
                    <option value="admin">Accès complet</option>
                </select>
            </div>
        </div>


        <div class="col-xs-12 col-sm-12 col-md-6">
        <label><small>Le mot de passe doit contenir 8 caractères, une majuscule, un chiffre et un caractère spécial au minimum.</small></label>

            <div class="form-group">
                <strong>Mot de passe:</strong>
                <input type="password" class="form-control form-control-user" name="password" id="password"  required  autocomplete="new-password"  pattern=".{8,30}"      id="exampleInputPassword" placeholder="Mot de passe*"  onchange="CheckPassword()" oninvalid="this.setCustomValidity('La taille minimale est 8 caractères')"   oninput="this.setCustomValidity('')"  >
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6">

        </div>

        <div class="col-xs-12 col-sm-12 col-md-6">
            <strong>Confirmation:</strong>
            <div class="form-group">
                <input type="password" class="form-control form-control-user" name="password_confirmation"  required id="password_confirmation"   autocomplete="new-password"   pattern=".{8,30}"    id="exampleRepeatPassword" placeholder="Confirmation du mot de passe*"  disabled>
            </div>
        </div>



        <div class="col-xs-12 col-sm-12 col-md-7">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </div>

</form>
@endsection


@section('footer-scripts')


<script>

    function CheckPassword()
    {
    var inputtxt=document.getElementById('password').value;
    //var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/;
    var passw = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/;
    //if(inputtxt.value.match(passw))
    if( passw.test(inputtxt))
    {
        $('#password').removeClass('is-invalid');
        $('#password').addClass('is-valid');


        $('#password').css('border','1px solid #18aa76');
        $("#password_confirmation").prop('disabled', false);
        $("#password_confirmation").focus();

    }
    else
    {
        $('#password').addClass('is-invalid');
        $('#password').removeClass('is-valid');

                        $('#password').css('border','2px solid #f1592a');
                        $('#password').focus();
                        $("#password_confirmation").prop('disabled', true);

    }
    }


</script>

@endsection