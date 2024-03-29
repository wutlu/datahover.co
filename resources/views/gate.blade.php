@extends(
	'layouts.master',
	[
		'title' => 'Gate'
	]
)

@push('css')
	html, body {
		height: 100%;
		background-color: #212529;
	}

	span.line {
		display: inline-block;
		width: 48px;
		height: 0;
		border-width: 2px 0 0;
	}
	span.line.dashed {
		border-style: dotted;
		border-color: #dc3545;
	}
	span.line.constant {
		border-style: solid;
		border-color: #212529;
	}

	img.github-icon {
		border: 2px dotted #dc3545;
		border-radius: 50%;
		padding: 2px;
	}
@endpush

@section('content')
	<div class="d-flex flex-column align-items-center justify-content-center h-100">
		<a title="Home" href="{{ route('index') }}">
			<img class="w-200px mb-4" alt="{{ config('app.name') }}" src="{{ asset('images/logo-white.svg') }}" />
		</a>
		<div class="mw-300px card shadow-sm mb-2">
			<div class="card-body">
				<div class="p-2">
					<div class="d-flex align-items-center justify-content-center mb-4">
						<div class="w-48px h-48px bg-dark text-white rounded-circle d-flex align-items-center justify-content-center">
							<i class="material-icons icon-lg">person</i>
						</div>
						<span class="line constant"></span>
						<a title="Close" href="{{ route('index') }}" class="w-32px h-32px bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mx-1">
							<i class="material-icons">clear</i>
						</a>
						<span class="line dashed"></span>
						<img alt="GitHub" src="{{ asset('images/icons/github.png') }}" width="64" height="64" class="github-icon" />
					</div>
					@if (Session::has('error'))
						<p class="alert text-danger border-0 text-center">{{ Session::get('error') }}</p>
					@else
						<p class="mb-3 text-muted small">{{ config('app.name') }} is a technical system that only concerns developers. Therefore, you can only sign up or login with GitHub. Click the button below and if you are not registered with {{ config('app.name') }}, we will create an account for you using your GitHub information. All users who go beyond this stage are deemed to have read and accepted the following rule pages.</p>
						<div class="d-flex flex-column mb-3">
							<a title="Privacy Policy" class="small" href="{{ route('page', [ 'base' => 'legal', 'name' => 'privacy-policy' ]) }}" class="text-dark text-decoration-none">Privacy Policy</a>
							<a title="Terms of Service" class="small" href="{{ route('page', [ 'base' => 'legal', 'name' => 'terms-of-service' ]) }}" class="text-dark text-decoration-none">Terms of Service</a>
						</div>
					@endif
					<a title="Login" href="{{ route('user.gate.redirect') }}" class="btn btn-outline-primary d-block shadow-sm">Continue with GitHub</a>
				</div>
			</div>
		</div>
		<div class="w-100 mw-300px d-flex align-items-start justify-content-between">
			<small class="text-muted mb-0">© {{ date('Y') }}</small>
			<img alt="{{ config('app.name') }}" src="{{ asset('images/logo-grey.svg') }}" width="96" class="h-auto" />
		</div>
	</div>
@endsection
