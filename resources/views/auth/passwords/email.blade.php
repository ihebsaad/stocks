@extends('layouts.newlogin')

@section('content')
  <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Mot de passe oublié?</h1>
                                        <p class="mb-4">Pas de problème, insérez votre adresse email ci dessous et nous allons vous envoyer 
										un lien pour reinitialiser votre mot de passe.
										 </p>
											      @if (session('status'))
													<div class="alert alert-success">
													{{ session('status') }}
													</div>
													@endif
                                    </div>
                                    <form class="user"   method="POST" action="{{ route('password.email') }}">
										{{ csrf_field() }}
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"  name="email" value="{{ old('email') }}" required
                                                id="email" aria-describedby="emailHelp"
                                                placeholder="Entrez votre adresse email ...">
												
												@if ($errors->has('email'))
												<span class="help-block">
												<strong>{{ $errors->first('email') }}</strong>
												</span>
												@endif
												
                                        </div>

                                        <div class="g-recaptcha mb-20" 
                                         data-sitekey="{{env('GOOGLE_RECAPTCHA_KEY')}}">
                                        </div>
                                        <script src='https://www.google.com/recaptcha/api.js'></script>

                                        
                                        <button type="submit"  class="btn btn-primary btn-user btn-block"  >
                                           Envoyer  
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="{{route('register')}}">Créer un compte</a>
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

        </div>

    </div>

@endsection
