<!DOCTYPE html>
<html  >

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="{{env('APP_AUTHOR')}}">

    <title>{{env('APP_NAME')}} </title>
    <link rel="shortcut icon" type="image/png" href="{{  URL::asset('img/logo.png') }}">
 
    
    <link href="https://fonts.googleapis.com/css?family=Heebo:400,700|IBM+Plex+Sans:600" rel="stylesheet">
 	<link rel="stylesheet" href="{{ URL::asset('switch/css/style.css') }}">

    <script src="https://unpkg.com/scrollreveal@4.0.0/dist/scrollreveal.min.js"></script>
</head>
 
@yield('content')
 
<script  src="{{ URL::asset('switch/js/main.min.js') }}"  ></script>

</body>

</html>
