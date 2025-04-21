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
                                        <p class="mb-4">Insérez votre adresse email mot de passe, et la confirmation du mot de passe</p>
                                    </div>
                                    <form method="POST" action="{{ route('password.update') }}" class="user">
									{{ csrf_field() }}
										<input type="hidden" name="token" value="{{ $token }}">

                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"  placeholder="Addresse Email"
											id="email"   class="form-control" name="email" required    value="{{ $email ?? old('email') }}" 
                                                 >
                                        </div>
										
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"  placeholder="Mot de passe"
											id="password"   class="form-control" name="password" required
                                                 >
                                        </div>
										@if ($errors->has('password'))
											<span class="help-block">
												<strong>{{ $errors->first('password') }}</strong>
											</span>
										@endif
										 <div class="form-group">
                                            <input type="password" class="form-control form-control-user" placeholder="Confirmation du mot de passe"
											id="password-confirm"   class="form-control" name="password_confirmation" required
                                                 >
                                        </div>
										@if ($errors->has('password_confirmation'))
										<span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
										</span>
										@endif
                                        <button   class="btn btn-primary btn-user btn-block">
                                            Changer le mot de passe
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="{{route('register')}}">Créer un compte</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small"  href="{{route('login')}}">Vous avez un compte? Connexion ici</a>
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
