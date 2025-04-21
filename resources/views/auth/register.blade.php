@extends('layouts.newlogin')

@section('content')
<style>
#type:placeholder-shown{
color: darkgrey;
}	
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
   <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
  <script>
  $( function() {
    $( "#naissance" ).datepicker(
        {
      changeMonth: true,
      changeYear: true
    }
    );
  } );
  $( "#naissance" ).datepicker( "option", "dateFormat", "dd/mm/yy" );

  </script>

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-2">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Créer mon compte</h1>
                            </div>
                            <form class="user"  method="POST" action="{{ route('register') }}">
								{{ csrf_field() }}
								
 @if ($errors->any())
             <div class="alert alert-danger">
                 <ul>
                     @foreach ($errors->all() as $error)
                         <li>{{ $error }}</li>
                     @endforeach
                 </ul>
             </div><br />
         @endif

    @if (!empty( Session::get('success') ))
        <div class="alert alert-success">

        {{ Session::get('success') }}
        </div>
    @endif								
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" id="exampleFirstName" name="name" required  oninvalid="this.setCustomValidity('Champ Obligatoire')"   oninput="this.setCustomValidity('')"  value="{{old('name')}}"  placeholder="Prénom*">
								@if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" id="exampleLastName" name="lastname" required   oninvalid="this.setCustomValidity('Champ Obligatoire')"  oninput="this.setCustomValidity('')"  value="{{old('lastname')}}"     placeholder="Nom*">
                                    </div>
                                @if ($errors->has('lastname'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lastname') }}</strong>
                                    </span>
                                @endif									
                                </div>


								
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">								

                                        <input type="email" class="form-control form-control-user" id="email" name="email"  required   Autocomplete="NoAutocomplete"       value="{{old('email')}}"  placeholder="Adresse Email*"   oninvalid="this.setCustomValidity('Insérez une adresse email valide')"
  oninput="this.setCustomValidity('')">	    
									</div>	
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                    <textarea   class="form-control form-control-user" id="adresse" name="adresse"   placeholder="Adresse Postale"   >{{old('adresse')}}</textarea>	

									</div>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
								
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                                </div>	
								
 

								<div class="row pl-20 pr-20">
								<label><small>Le mot de passe doit contenir 8 caractères, une majuscule, un chiffre et un caractère spécial au minimum.</small></label>
								</div>
								<div class="form-group row">

                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user" name="password" id="password"  required  autocomplete="new-password"  pattern=".{8,30}"
                                            id="exampleInputPassword" placeholder="Mot de passe*"  onchange="CheckPassword()" oninvalid="this.setCustomValidity('La taille minimale est 8 caractères')"   oninput="this.setCustomValidity('')"  >
                                    </div>
									
								
                                    <div class="col-sm-6">
                                         <input type="password" class="form-control form-control-user" name="password_confirmation"  required id="password_confirmation"   autocomplete="new-password"   pattern=".{8,30}"
                                            id="exampleRepeatPassword" placeholder="Confirmation du mot de passe*"  disabled>
                                    </div>
							   @if ($errors->has('password') )
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif

							   @if ($errors->has('password_confirmation') )
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif									
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block"  id="register" >
                                    Inscription
                                </button>
                                 
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="{{ route('password.request') }}">Mot de passe oublié?</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="{{route('login')}}">Vous avez un compte? Connexion ici</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    

	<style>
	.btn:disabled{opacity:0.5;}
	</style>
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

      /*              $.notify({
                        message: 'Mot de passe valide  !',
                        icon: 'glyphicon glyphicon-remove'
                    },{
                        type: 'success',
                        delay: 1000,
                        timer: 3000,
                        placement: {
                            from: "bottom",
                            align: "right"
                        },
                    }); */
  	 $('#password').css('border','1px solid #18aa76');																	
  	 $("#password_confirmation").prop('disabled', false);
	 
}
else
{ 
    $('#password').addClass('is-invalid'); 
    $('#password').removeClass('is-valid');
		/*           $.notify({
                        message: 'Mot de passe faible  !',
                        icon: 'glyphicon glyphicon-remove'
                    },{
                        type: 'danger',
                        delay: 1000,
                        timer: 3000,
                        placement: {
                            from: "bottom",
                            align: "right"
                        },
                    }); */
					 $('#password').css('border','2px solid #f1592a');					
 					$('#password').focus();
					 $("#password_confirmation").prop('disabled', true);

}
}
  
		
 
		
		$( "#username" ).keypress(function( evt ) {
		
     var ASCIICode = (evt.which) ? evt.which : evt.keyCode 
        if (ASCIICode >= 65 &&  ASCIICode <= 120  && ASCIICode > 57  && ASCIICode != 32  && ASCIICode != 0  ) 
            return true; 
        return false; 
		});	
		


	
	
	$( "#password_confirmation" ).change(function() {
		password=$('#password').val();
		confirm=$('#password_confirmation').val();
					if(password!=confirm ){
		            /*        $.notify({
                        message: 'Les mots de passes sont différents !',
                        icon: 'glyphicon glyphicon-remove'
                    },{
                        type: 'danger',
                        delay: 1000,
                        timer: 3000,
                        placement: {
                            from: "bottom",
                            align: "right"
                        },
                    });*/
                        $('#password_confirmation').addClass('is-invalid'); 
                        $('#password_confirmation').removeClass('is-valid');

					$('#password_confirmation').val('');
					$('#password_confirmation').focus();
					}else{

                        $('#password_confirmation').addClass('is-valid'); 
                         $('#password_confirmation').removeClass('is-invalid');  
                    }
			});		
	
 
	</script>


@endsection