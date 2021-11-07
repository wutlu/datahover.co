@extends(
	'layouts.master'
)

@push('css')
	header.master {
		background-image: radial-gradient(circle farthest-corner at 32.7% 82.7%, #1b1f24 8.3%, #111 79.4%);
		width: 100%;
		position: relative;
		padding: 10vw 0 0;
	}

	header.master img[alt=Logo] {
		width: 30%;
		max-width: 164px;
	}

	.console {
		width: 100%;
		height: 400px;
		max-width: 800px;
		background-color: #232a2f;
		border-radius: .8rem .8rem 0 0;
		border-style: solid;
		border-width: 1px 1px 0 1px;
		border-color: #000;
		margin: 0 auto;
		overflow: hidden;
	}
	.console > .nav {
		width: 100%;
		background-color: #1a2023;
		padding: 0 .8rem;
	}
	.console > ul.nav > li.nav-item > a.nav-link {
		font-size: 14px;
		border-right: 1px solid #000;
	}
	.console > ul.nav > li.nav-item > a.nav-link.active {
		box-shadow: inset 0 1px 0 0 #dc3461;
		color: #666;
	}
	.console > .wrapper {
		max-height: 100%;
		overflow: hidden;
	}
	.console > .wrapper > p {
		font-size: 13px;
		font-family: Arial;
		letter-spacing: 1px;
	}
	.console > .wrapper > p > time {
		display: inline-block;
		width: 54px;
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

	.card.card-mini {
		max-width: 1024px;
		margin: 100px auto;
	}

	.card.card-mini > .card-body {
		z-index: 1;
	}
	.card.card-mini > img[alt=Logo] {
		z-index: 0;
		opacity: .1;
		width: 1600px;
	}

	.pool {
		width: 300px;
		height: 300px;
		border-radius: 50%;
		background-image: linear-gradient(to top, #0ba360 0%, #3cba92 100%);
		margin: 0 auto;
		animation-name: example;
		animation-duration: 10s;
		animation-iteration-count: infinite;
		font-size: 24px;
		letter-spacing: -1px;
	}

	@media (max-width: 720px)
	{
		.pool {
			width: 100px;
			height: 240px;
			border-radius: 50%;
			font-size: 16px;
		}
	}

	@keyframes example {
		0% { transform: scale(1); }
		30% { transform: scale(1.1) skew(10deg); }
		60% { transform: scale(1.2) skew(-10deg); }
		100% { transform: scale(1); }
	}

	.bg-info {
		background-color: #0ba360 !important;
	}
	.text-info {
		color: #0ba360 !important;
	}
@endpush

@push('js')
	setInterval(function() {
		let currentdate = new Date();
		let fullTime = currentdate.getHours() + ':' + currentdate.getMinutes() + ':' + currentdate.getSeconds();

		$('.console > .wrapper').append(
			$(
				'<p />',
				{
					'html': '<time>' + fullTime + '</time>: {"text": "lorem textsum...", "key": "' + Math.random() + '"}',
					'class': 'text-success px-4 py-1 mb-0'
				}
			)
		)
		.scrollTop($(".console > .wrapper")[0].scrollHeight);
	}, 100)
@endpush

@push('footer')
	@include('includes.modals.track_info')
@endpush

@section('content')
	<header class="master">
		<div class="container">
			<div class="d-flex align-items-center mb-5">
				<img alt="Logo" src="{{ asset('images/logo.svg') }}" width="auto" height="auto" />

				<div class="dropdown ms-auto">
					<a href="#" class="dropdown-toggle link-light d-flex align-items-center gap-2" type="button" id="user-menu" data-bs-toggle="dropdown" aria-expanded="false">
						@auth
							<img alt="Avatar" src="{{ auth()->user()->avatar }}" class="w-32px h-32px rounded-circle" />
							{{ auth()->user()->name }}
						@else
							<i class="material-icons">account_circle</i> My Account
						@endauth
					</a>
					<ul class="dropdown-menu rounded-0 shadow dropdown-menu-dark dropdown-menu-end" aria-labelledby="user-menu">
						@auth
							<li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
							<li><a class="dropdown-item" href="{{ route('user.account') }}">Account</a></li>
							<li class="divider bg-dark"></li>
							<li><a class="dropdown-item" href="{{ route('user.gate.exit') }}">Logout</a></li>
						@else
							<li><a class="dropdown-item" href="{{ route('user.gate') }}">Login</a></li>
						@endauth
					</ul>
				</div>
			</div>
			<div class="text-center mb-5">
				<h1 class="text-white fw-bold mb-2">Voluminous and quite simple</h1>
				<p class="lead text-muted mb-5">Access open source internet data in a simple way with DATAHOVER</p>
				@auth
					<a href="{{ route('dashboard') }}" class="btn btn-lg btn-outline-primary rounded-pill rounded-0">Dashboard</a>
				@else
					<a href="{{ route('user.gate') }}" class="btn btn-lg btn-outline-primary rounded-pill rounded-0">Start a free trial</a>
				@endif
			</div>
			<div class="console">
				<ul class="nav">
					<li class="nav-item">
						<a class="nav-link active" aria-current="page" href="#">Console</a>
					</li>
				</ul>
				<div class="wrapper"></div>
			</div>
		</div>
		<svg xmlns="http://www.w3.org/2000/svg" fill="none" preserveAspectRatio="none" viewBox="0 0 1680 40" class="position-absolute width-full z-1" style="bottom: -1px;"><path d="M0 40h1680V30S1340 0 840 0 0 30 0 30z" fill="#f0f0f0"></path></svg>
	</header>

	<div class="container">
		<div class="card card-mini overflow-hidden position-relative shadow-lg border-0 rounded-0">
			<div class="card-body">
				<div class="p-4">
					<div class="mb-4">
						@foreach (config('sources') as $key => $item)
							<img data-bs-toggle="tooltip" title="{{ $item['name'] }}" alt="{{ $item['name'] }}" src="{{ asset($item['icon']) }}" width="48" height="48" />
						@endforeach
					</div>
					<h4 class="fw-bold text-dark mb-0">Access big data at a very low cost</h4>
					<p class="text-muted d-block mb-2">You can access our entire database for a low fee.</p>
					<div class="d-flex gap-2">
						@auth
							<a href="{{ route('dashboard') }}" class="btn btn-outline-dark shadow-sm rounded-0">Dashboard</a>
						@else
							<a href="{{ route('user.gate') }}" class="btn btn-outline-dark shadow-sm rounded-0">Start a free trial</a>
						@endauth
					</div>
				</div>
			</div>
			<img alt="Logo" class="position-absolute" src="{{ asset('images/logo.svg') }}" />
		</div>
	</div>

	<div class="bg-dark">
		<div class="container">
			<div class="my-5 py-5 text-center">
				<h3 class="text-light fw-bold mb-5">How does it work?</h3>
				<div class="d-flex align-items-center justify-content-between mb-5">
					<div class="text-white d-flex flex-column gap-4">
						<span class="d-flex flex-column align-items-center">
							<i class="material-icons icon-lg">face</i>
							Leila criteria
							<div class="d-flex flex-wrap gap-1 justify-content-center">
								<small class="badge rounded-pill bg-info text-dark">Netflix</small>
								<small class="badge rounded-pill bg-info text-dark">Carrefour</small>
								<small class="badge rounded-pill bg-info text-dark">Tesla</small>
							</div>
						</span>
						<span class="d-flex flex-column align-items-center">
							<i class="material-icons icon-lg">sentiment_satisfied_alt</i>
							Hector criteria
							<div class="d-flex flex-wrap gap-1 justify-content-center">
								<small class="badge rounded-pill bg-info text-dark">Election</small>
								<small class="badge rounded-pill bg-info text-dark">Politics</small>
								<small class="badge rounded-pill bg-info text-dark">Vote</small>
							</div>
						</span>
						<span class="d-flex flex-column align-items-center">
							<i class="material-icons icon-lg">insert_emoticon</i>
							Paul criteria
							<div class="d-flex flex-wrap gap-1 justify-content-center">
								<small class="badge rounded-pill bg-info text-dark">Coffe</small>
								<small class="badge rounded-pill bg-info text-dark">Arabica</small>
								<small class="badge rounded-pill bg-info text-dark">Starbucks</small>
							</div>
						</span>
					</div>
					<div class="d-flex align-items-center">
						<div class="d-flex flex-column align-items-start text-white">
							<i class="material-icons">south_east</i>
							<i class="material-icons icon-lg">east</i>
							<i class="material-icons">north_east</i>
						</div>
					</div>
					<div class="d-flex align-items-center">
						<div class="pool d-flex flex-column align-items-center justify-content-center">
							<span class="text-white">Realtime Workers</span>
							<i class="material-icons">arrow_downward</i>
							<span>Big Data Pool</span>
						</div>
					</div>
					<div class="d-flex align-items-center">
						<i class="material-icons icon-lg text-white">east</i>
					</div>
					<div class="text-white d-flex flex-column">
						<span class="d-flex flex-column align-items-center">
							<i class="material-icons icon-lg">insights</i>
							Limitless filterable data APi
						</span>
						<span class="text-info">An advanced dashboard</span>
					</div>
				</div>
				<div class="p-4 text-white">
					<p class="lead">What types of APi services are there?</p>
					<div class="d-flex align-items-center justify-content-center gap-4">
						<span class="d-flex flex-column justify-content-center">
							<div class="d-flex align-items-center justify-content-center w-32px h-32px bg-success rounded-circle mx-auto mb-2">
								<i class="material-icons text-dark">add</i>
							</div>
							<small class="text-nowrap">Track Create</small>
						</span>
						<span class="d-flex flex-column justify-content-center">
							<div class="d-flex align-items-center justify-content-center w-32px h-32px bg-danger rounded-circle mx-auto mb-2">
								<i class="material-icons text-dark">clear</i>
							</div>
							<small class="text-nowrap">Track Delete</small>
						</span>
						<span class="d-flex flex-column justify-content-center">
							<div class="d-flex align-items-center justify-content-center w-32px h-32px bg-white rounded-circle mx-auto mb-2">
								<i class="material-icons text-dark">reorder</i>
							</div>
							<small class="text-nowrap">Track List</small>
						</span>
						<span class="d-flex flex-column justify-content-center">
							<div class="d-flex align-items-center justify-content-center w-32px h-32px bg-white rounded-circle mx-auto mb-2">
								<i class="material-icons text-dark">search</i>
							</div>
							<small class="text-nowrap">Data Search</small>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="my-5 py-5 text-center">
			<h3 class="text-dark fw-bold mb-5">Pricing</h3>
			<div class="row row-cols-1 row-cols-md-3 mb-4 align-items-center">
				<div class="col">
					<div class="card mb-4 border-0 rounded-0 bg-transparent">
						<div class="card-header bg-transparent border-0 pt-4">
							<span class="my-0 fw-bold">{{ config('subscriptions.basic.name') }}</span>
						</div>
						<div class="card-body">
							<h3 class="card-title pricing-card-title display-6">${{ config('subscriptions.basic.price') }}<small class="text-muted fw-light">/mo</small></h3>
							<ul class="list-unstyled mt-3 mb-4">
								<li class="d-inline-flex align-items-center gap-2">
									<span>{{ config('subscriptions.basic.track_limit') }} Track</span>
									<a href="#" data-bs-toggle="modal" data-bs-target="#trackInfoModal" class="text-dark"><i class="material-icons">info</i></a>
								</li>
								<li class="d-block">
									<span>Limitless query</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card mb-4 border-0 rounded-0 shadow-sm" style="border-color: var(--bs-blue);">
						<div class="card-header bg-transparent border-0 pt-4">
							<span class="my-0 fw-bold">{{ config('subscriptions.pro.name') }}</span>
						</div>
						<div class="card-body">
							<h3 class="card-title pricing-card-title display-2">${{ config('subscriptions.pro.price') }}<small class="text-muted fw-light">/mo</small></h3>
							<ul class="list-unstyled mt-3 mb-4">
								<li class="d-inline-flex align-items-center gap-2">
									<span>{{ config('subscriptions.pro.track_limit') }} Track</span>
									<a href="#" data-bs-toggle="modal" data-bs-target="#trackInfoModal" class="text-dark"><i class="material-icons">info</i></a>
								</li>
								<li class="d-block">
									<span>Limitless query</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card mb-4 border-0 rounded-0 bg-transparent">
						<div class="card-header bg-transparent border-0 pt-4">
							<span class="my-0 fw-bold">{{ config('subscriptions.enterprise.name') }}</span>
						</div>
						<div class="card-body">
							<h3 class="card-title pricing-card-title display-6">${{ config('subscriptions.enterprise.price') }}<small class="text-muted fw-light">/mo</small></h3>
							<ul class="list-unstyled mt-3 mb-4">
								<li class="d-inline-flex align-items-center gap-2">
									<span>{{ config('subscriptions.enterprise.track_limit') }} Track</span>
									<a href="#" data-bs-toggle="modal" data-bs-target="#trackInfoModal" class="text-dark"><i class="material-icons">info</i></a>
								</li>
								<li class="d-block">
									<span>Limitless query</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			@auth
				<a href="{{ route('dashboard') }}" class="btn btn-lg shadow-sm px-4 btn-outline-primary rounded-0">Dashboard</a>
			@else
				<a href="{{ route('user.gate') }}" class="btn btn-lg shadow-sm px-4 btn-outline-primary rounded-0">Start a free trial</a>
			@endauth
		</div>
	</div>

{{-- 	<div class="bg-dark">
		<div class="container">
			<div class="d-flex justify-content-center align-items-center flex-wrap gap-4 my-5 py-5">
				<a href="#">
					<img alt="Logo" src="{{ asset('images/brands/btk.png') }}" width="100" height="auto" />
				</a>
				<a href="#">
					<img alt="Logo" src="{{ asset('images/brands/medyaizi.png') }}" width="100" height="auto" />
				</a>
				<a href="#">
					<img alt="Logo" src="{{ asset('images/brands/geometry.png') }}" width="100" height="auto" />
				</a>
			</div>
		</div>
	</div> --}}

	<footer class="bg-dark">
		<div class="container">
			<div class="py-5">
				<div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-between gap-4">
					<div class="d-flex flex-column text-center text-lg-start">
						<img alt="Logo" src="{{ asset('images/logo-grey.svg') }}" width="164" height="auto" class="mx-auto mx-lg-0 mb-2" />
						<small class="mb-4 text-muted mw-300px">{{ config('etsetra.info') }}</small>
						<p class="mb-0 text-muted">© {{ date('Y') }} {{ config('app.name') }}</p>
					</div>
					<div class="d-flex flex-column align-items-center align-items-lg-start">
						<a href="#" class="link-light">About Us</a>
						<a href="#" class="link-light">Public Offer Agreement</a>
						<a href="#" class="link-light">Privacy Policy</a>
					</div>
					<div class="d-flex flex-column text-center text-lg-end">
						<small class="mb-2 text-muted mw-300px">{{ config('etsetra.address') }}</small>
						<a href="#" class="text-muted">{{ config('etsetra.email') }}</a>
					</div>
				</div>
			</div>
		</div>
	</footer>
@endsection
