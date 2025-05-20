
@include('layouts.partial.head')

<body class="hold-transition sidebar-mini layout-fixed  {{ (strpos($view_name, 'tests') !== false)  ? 'sidebar-collapse' : '' }} ">
	<div class="wrapper">

		 <!-- Preloader -->
		<div class="preloader flex-column justify-content-center align-items-center">
			<img class="animation__shake" src="{{asset('img/logo.png')}}" alt="logo" height="auto" width="250">
		</div>
	@auth
	@include('layouts.partial.top-nav')
	@include('layouts.partial.menu')
	@endauth
    <div class="content-wrapper">

	<!-- Main content -->
    <section class="content pt-3 pb-3">
      <div class="container-fluid" style="background-color:white;padding:2% 2%;border-radius: 15px;">

	    @if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
		@endif

		@if (session('success'))
			<div class="alert alert-success">
				{{ session('success') }}
			</div>
		@endif
		
		@if (session('error'))
			<div class="alert alert-danger">
				{{ session('error') }}
			</div>
		@endif
		@yield('content')


	  </div>
	</section>

	</div>

	@include('layouts.partial.footer')

	</div>

	 @include('layouts.partial.footer-scripts')

</body>
</html>
