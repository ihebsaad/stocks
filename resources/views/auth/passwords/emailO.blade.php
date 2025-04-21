@extends('layouts.login')

@section('content')
<div class="container">
    <div class="row"  style="margin-top:5%">

        <div class="col-md-8 col-md-offset-2">
<center><img style="margin-bottom:30px;" src="{{  URL::asset('public/img/logo.png') }}" alt="SAAMP" class="img-circle" width="200"></center>

            <div class="panel panel-default">
                <div class="panel-heading" style="color:black;font-weight:800;font-size:20px;text-align:center;">Réinitialiser le mot de passe
                </div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                   Envoyer le lien
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
