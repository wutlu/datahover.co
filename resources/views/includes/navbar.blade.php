<nav class="master shadow-sm bg-dark position-sticky top-0 right-0 left-0">
	<div class="container">
		<div class="d-flex justify-content-between align-items-center h-50px">
			<a title="Home" href="{{ route('dashboard') }}">
				<img alt="Logo" src="{{ asset('images/logo-white.svg') }}" class="w-96px" />
			</a>

			<div class="d-flex align-items-center gap-2">
				<div class="d-inline-block d-md-none">
					<a title="Menu" href="#" class="link-light drawer" data-class="body" data-class-toggle="drawer">
						<i class="material-icons--before"></i>
					</a>
				</div>
				@include('includes.user_menu')
			</div>
		</div>
	</div>
</nav>
