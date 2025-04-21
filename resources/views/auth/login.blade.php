@extends('layouts.newlogin')

@section('content')

    <div class="container" style="padding-top:5%">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Connexion </h1>
                                    </div>
                                    <form class="user"   method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}
						
                                        <div class="form-group">
                                            <input   class="form-control form-control-user  form-control{{ $errors->has('username') || $errors->has('email') ? ' is-invalid' : '' }}"
                                                id="login" aria-describedby="emailHelp"
                                                placeholder="Votre identifiant ou adresse email"   name="email" value="{{ old('username') ?: old('email') }}" required autofocus>
                                  @if ($errors->has('username') || $errors->has('email'))
                                    <span class="invalid-feedback">
								<strong>{{ $errors->first('username') ?: $errors->first('email') }}</strong>
									</span>
                                @endif                                     
									   </div>
                                        <div class="form-group">
                                            <input type="password" name="password"  class="form-control form-control-user"
                                                id="pasword" placeholder="Mot de passe">
												
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif												
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck"    name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="customCheck">Se souvenir de moi 
                                                    </label>
                                            </div>
                                        </div>

                                        
                                        <button type="submit"  class="btn btn-primary btn-user btn-block">
                                            Connexion
                                        </button>
 
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="{{ route('password.request') }}">Mot de passe oublié?</a>
                                    </div>
                                    <div class="text-center">
                                        <!--<a class="small" href="{{ route('register') }}">Créer un compte!</a>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
	
	
@endsection