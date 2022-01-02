@push('css')
	header.master {
		background-image: url('{{ asset('images/wave-dark.svg') }}');
		background-repeat: no-repeat;
		background-position: center bottom;
		background-size: cover;
		background-attachment: fixed;
		background-color: #1c1c28;
		width: 100%;
		position: relative;
		padding: 10vw 0 0;
	}

	header.master .logo {
		width: 30%;
		max-width: 164px;
	}

	h1 {
		font-size: 72px;
	}

	@media (max-width: 720px)
	{
		h1 {
			font-size: 48px;
		}
	}
@endpush

<header class="master">
	<div class="container">
		<div class="d-flex align-items-center mb-5">
			<a href="{{ route('index') }}" class="logo">
				<img alt="Logo" src="{{ asset('images/logo-white.svg') }}" width="100%" height="auto" />
			</a>
			@include('includes.user_menu')
		</div>
		<h1 class="text-center text-white fw-bold mb-0">{{ $title }}</h1>
		{{ $slot }}
	</div>
	<svg xmlns="http://www.w3.org/2000/svg" fill="none" preserveAspectRatio="none" viewBox="0 0 1680 40" class="position-absolute width-full z-1" style="bottom: -1px;"><path d="M0 40h1680V30S1340 0 840 0 0 30 0 30z" fill="#f8f9fa"></path></svg>
</header>
