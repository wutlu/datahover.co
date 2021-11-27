@extends(
	'layouts.master'
)

@push('css')
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
		scroll-behavior: smooth;
	}
	.console > .wrapper > p {
		font-size: 13px;
		font-family: 'Courier', sans-serif;
		letter-spacing: 1px;
	}
	.console > .wrapper > p > time {
		display: inline-block;
		width: 190px;
	}

	.pool {
		font-size: 24px;
		letter-spacing: -1px;
	}

	@media (max-width: 720px)
	{
		.pool {
			font-size: 16px;
		}
	}

	.bg-info {
		background-color: #6159F6 !important;
	}
	.bg-dark {
		background-color: #1c1c28 !important;
	}
	.text-info {
		color: #6159F6 !important;
	}
@endpush

@push('js')
	let items = [];
	let consoleTimer;

	function __results(__, obj)
	{
		window.clearTimeout(consoleTimer);

		$.each(obj.data, function(k, item) {
			items.push(item)
		})
	}

	let consoleWrapper = $('.console').children('.wrapper');

	window.consoleTimer = setInterval(function() {
		if (items.length)
		{
			consoleWrapper.append(
				$(
					'<p />',
					{
						'html': '<time>' + $(items).get(-1).created_at + '</time>: ' + $(items).get(-1).title,
						'class': 'text-success px-4 mb-0'
					}
				)
				.hide()
				.fadeIn()
			)
			.scrollTop(consoleWrapper[0].scrollHeight);

			items.pop();
		}
	}, 400)
@endpush

@push('footer')
	@include('includes.modals.track_info')

	<script src="{{ asset('js/revolving.min.js') }}"></script>
@endpush

@section('content')
	@component('includes.header')
		@slot('title', 'Voluminous and quite simple')
		<p class="lead text-muted mb-5 text-center">Access open source internet data in a simple way with {{ Str::upper(config('app.name')) }}</p>
		<div class="d-flex justify-content-center mb-5">
			@auth
				<a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-lg rounded-pill rounded-0">Dashboard</a>
			@else
				<a href="{{ route('user.gate') }}" class="btn btn-outline-primary btn-lg rounded-pill rounded-0">Start a free trial</a>
			@endif
		</div>
		<div class="console">
			<ul
				id="items"
				class="nav load"
	            data-action="{{ route('index.console') }}"
	            data-callback="__results"
	            data-loop="10000">
				<li class="nav-item">
					<a class="nav-link active" aria-current="page" href="#">Console</a>
				</li>
			</ul>
			<div class="wrapper"></div>
		</div>
	@endcomponent

	@php
	$code = '{
    "id": "f8b627242b1bc64a8...",
    "site": "nytimes.com",
    "link": "nytimes.com/2021/1...",
    "device": "Web",
    "status": "ok",
    "created_at": "2021-11-12T03:13:40+00:00",
    "called_at": "2021-11-12T03:15:52+00:00",
    "image": "https://static01.nyt.co...",
    "title": "Lorem ipsum dolor...",
    "article": "Lorem ipsum dolor sit amet...",
    "lang": "en"
}';
	@endphp

	<div class="container">
		<div class="card border-0 bg-transparent mw-1024px mx-auto">
			<div class="card-body">
				<h3 class="card-title fw-bold dotted-title py-4">What is Datahover</h3>

				<div class="row mb-4">
					<div class="col-12 col-sm-6 text-center text-sm-start d-flex align-items-center">
						<div class="p-4">
							<span class="lead">You do not need to specify any details, whether it is Rss or Api or not. You just need to specify the domain. <strong>We provide api output of news and blog sites.</strong></span>
						</div>
					</div>
					<div class="col-12 col-sm-6 text-center text-sm-end">
						<img alt="Create Track" src="{{ asset('images/create-track.png') }}" class="img-fluid shadow-sm" />
					</div>
				</div>

				<div class="row">
					<div class="col-12 col-sm-6 d-flex flex-column align-items-center justify-content-center">
						<div class="d-flex flex-wrap align-items-center justify-content-center gap-4">
							@foreach (config('sources') as $key => $item)
								<div class="bg-dark p-3 rounded-circle">
									<img
										data-bs-toggle="tooltip"
										title="{{ $item['name'] }}"
										alt="{{ $item['name'] }}"
										src="{{ asset($item['icon']) }}"
										width="48"
										height="48" />
								</div>
							@endforeach
						</div>
					</div>
					<div class="col-12 col-sm-6 text-center text-sm-start d-flex align-items-center">
						<div class="p-4">
							<p class="lead mb-0">You specify the word, we will follow the social media platforms for you. You can easily reach the results we found with our Api services.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="bg-dark bg-alternate">
		<div class="container">
			<div class="my-5 py-5 text-center">
				<h3 class="text-light fw-bold mb-5">How does it work?</h3>
				<div class="d-flex align-items-center justify-content-between mb-5">
					<div class="text-white d-flex flex-column gap-4">
						<span class="d-flex flex-column align-items-center">
							<i class="material-icons icon-lg">face</i>
							Leila's criteria
							<div class="d-flex flex-wrap gap-1 justify-content-center">
								<small class="badge rounded-pill bg-info text-dark">Netflix</small>
								<small class="badge rounded-pill bg-info text-dark">Carrefour</small>
								<small class="badge rounded-pill bg-info text-dark">Tesla</small>
							</div>
						</span>
						<span class="d-flex flex-column align-items-center">
							<i class="material-icons icon-lg">sentiment_satisfied_alt</i>
							Hector's criteria
							<div class="d-flex flex-wrap gap-1 justify-content-center">
								<small class="badge rounded-pill bg-info text-dark">Election</small>
								<small class="badge rounded-pill bg-info text-dark">Politics</small>
								<small class="badge rounded-pill bg-info text-dark">nytimes.com</small>
							</div>
						</span>
						<span class="d-flex flex-column align-items-center">
							<i class="material-icons icon-lg">insert_emoticon</i>
							Paul's criteria
							<div class="d-flex flex-wrap gap-1 justify-content-center">
								<small class="badge rounded-pill bg-info text-dark">Coffe</small>
								<small class="badge rounded-pill bg-info text-dark">Arabica</small>
								<small class="badge rounded-pill bg-info text-dark">Starbucks</small>
							</div>
						</span>
					</div>
					<div class="d-flex align-items-center" style="z-index: 2;">
						<div class="d-flex flex-column align-items-start text-white">
							<i class="material-icons">south_east</i>
							<i class="material-icons icon-lg">east</i>
							<i class="material-icons">north_east</i>
						</div>
					</div>
					<div class="d-flex justify-content-center align-items-center">
						<div class="pool position-relative d-flex flex-column align-items-center justify-content-center" style="z-index: 2;">
							<span class="text-white">Realtime Workers</span>
							<i class="material-icons text-white">arrow_downward</i>
							<span class="text-white">Communal Data Pool</span>
						</div>
						<canvas class="rounded-circle position-absolute" id="revolving"></canvas>
					</div>
					<div class="d-flex align-items-center" style="z-index: 2;">
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

	<div class="card border-0 bg-transparent mw-1024px mx-auto">
		<div class="card-body">
			<div class="dotted-title py-4">
				<h3 class="card-title fw-bold">Pricing</h3>
				{{-- <div class="d-flex align-items-center justify-content-center gap-2">
					<label class="text-muted small unselectable" for="yearly">Monthly</label>
					<div class="form-check form-switch ms-2 mb-0">
						<input autocomplete="off" class="form-check-input rounded-0 border-2" type="checkbox" role="switch" id="yearly" />
					</div>
					<label class="text-primary small unselectable" for="yearly">Yearly <span class="badge bg-primary bg-opacity-25 text-primary rounded-0 ms-2">10% Off</span></label>
				</div> --}}
			</div>
		</div>
	</div>

	<div class="container">
		<div class="row row-cols-1 row-cols-md-3 align-items-center">
			<div class="col">
				<div class="card rounded-0 shadow-sm mb-4">
					<div class="card-body d-flex flex-column gap-3 p-4">
						<h4 class="card-title mb-0">{{ config('plans.basic.name') }}</h4>
						<div class="d-flex align-items-end gap-2">
							<span class="price fw-bold display-4">
								$<span data-name="price">{{ config('plans.basic.price') }}</span>
							</span>
							<small class="text-muted h4">/Month</small>
						</div>
						<ul class="list-unstyled d-flex flex-column gap-2 mb-5">
							<li class="d-inline-flex align-items-center gap-2">
								<i class="material-icons text-success">check</i>
								<span class="h6 mb-0">{{ config('plans.basic.track_limit') }} Track</span>
								<a href="#" data-bs-toggle="modal" data-bs-target="#trackInfoModal" class="text-dark"><i class="material-icons">info</i></a>
							</li>
							<li class="d-inline-flex align-items-center gap-2">
								<i class="material-icons text-success">check</i>
								<span class="h6 mb-0">Limitless filterable query</span>
							</li>
							<li class="d-inline-flex align-items-center gap-2">
								<i class="material-icons text-success">check</i>
								<span class="h6 mb-0">E-mail support</span>
							</li>
						</ul>
						<a href="{{ route('user.gate') }}" class="btn btn-primary rounded-0 shadow-sm">Choose Plan</a>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card rounded-0 shadow mb-4 border-3 border-primary">
					<div class="card-body d-flex flex-column gap-3 p-4">
						<small class="bg-light text-primary text-center py-2">Most Popular Plan</small>
						<h4 class="card-title mb-0">{{ config('plans.pro.name') }}</h4>
						<div class="d-flex align-items-end gap-2">
							<span class="price fw-bold display-4">
								$<span data-name="price">{{ config('plans.pro.price') }}</span>
							</span>
							<small class="text-muted h4">/Month</small>
						</div>
						<ul class="list-unstyled d-flex flex-column gap-2 mb-5">
							<li class="d-inline-flex align-items-center gap-2">
								<i class="material-icons text-success">check</i>
								<span class="h6 mb-0">{{ config('plans.pro.track_limit') }} Track</span>
								<a href="#" data-bs-toggle="modal" data-bs-target="#trackInfoModal" class="text-dark"><i class="material-icons">info</i></a>
							</li>
							<li class="d-inline-flex align-items-center gap-2">
								<i class="material-icons text-success">check</i>
								<span class="h6 mb-0">Limitless filterable query</span>
							</li>
							<li class="d-inline-flex align-items-center gap-2">
								<i class="material-icons text-success">check</i>
								<span class="h6 mb-0">E-mail support</span>
							</li>
						</ul>
						<a href="{{ route('user.gate') }}" class="btn btn-primary rounded-0 shadow-sm">Choose Plan</a>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card rounded-0 shadow-sm mb-4 text-white bg-dark">
					<div class="card-body d-flex flex-column gap-3 p-4">
						<h4 class="card-title mb-0 text-warning">{{ config('plans.enterprise.name') }}</h4>
						<div class="d-flex align-items-end gap-2">
							<span class="price fw-bold display-4">
								$<span data-name="price">{{ config('plans.enterprise.price') }}</span>
							</span>
							<small class="text-muted h4">/Month</small>
						</div>
						<ul class="list-unstyled d-flex flex-column gap-2 mb-5">
							<li class="d-inline-flex align-items-center gap-2">
								<i class="material-icons text-success">check</i>
								<span class="h6 mb-0">{{ config('plans.enterprise.track_limit') }} Track</span>
								<a href="#" data-bs-toggle="modal" data-bs-target="#trackInfoModal" class="text-white"><i class="material-icons">info</i></a>
							</li>
							<li class="d-inline-flex align-items-center gap-2">
								<i class="material-icons text-success">check</i>
								<span class="h6 mb-0">Limitless filterable query</span>
							</li>
							<li class="d-inline-flex align-items-center gap-2">
								<i class="material-icons text-success">check</i>
								<span class="h6 mb-0">E-mail support</span>
							</li>
						</ul>
						<a href="{{ route('user.gate') }}" class="btn btn-primary rounded-0 shadow-sm">Choose Plan</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card mw-768px mx-auto bg-transparent border-0 rounded-0 my-5">
		<div class="card-body">
			<h3 class="fw-bold text-dark mb-2 text-center">Need more information?</h3>
			<p class="lead mb-5 text-center">
				If you would like to get more details or have additional questions, there's probably an answer already on our <a class="link-dark fw-bold text-decoration-underline" href="{{ route('faq.index') }}">Frequently Asked Questions</a> page.
			</p>
			<div class="d-flex flex-column flex-sm-row align-items-center justify-content-center gap-3">
				<span class="text-success text-center h4 mb-0">If you don't want to waste time</span>
				<a href="{{ route(auth()->check() ? 'dashboard' : 'user.gate') }}" class="btn btn-outline-success shadow-sm rounded-0">Try it</a>
			</div>
		</div>
	</div>

	@include('includes.footer')
@endsection
