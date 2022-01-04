@extends(
	'layouts.master',
	[
		'description' => 'Datahover, provides data to those who need social media and news data.',
		'keywords' => 'social media and news data, make website API, fetch news sites, API builder, API for website, webpage to API, website to API, domain to API'
	]
)

@push('css')
	header.master {
		background-image: url('{{ asset('images/header-overlay.png') }}'),
						  url('{{ asset('images/glow.svg') }}'),
						  url('{{ asset('images/header-bg.webp') }}');
	}
	header.master a.logo {}
	header.master a.logo > .logo {
		width: 40vw;
		max-width: 200px;
	}

	.page-split {
		width: calc(100% - 10vw);
		margin: 5vh auto;
		padding: 10vh 10vw;
		border-radius: 1rem;

		background-repeat: repeat, no-repeat;
		background-position: center bottom;
		background-size: auto, cover;
	}

	@media (max-width: 1024px)
	{
		.page-split {
			width: 100%;
			min-height: auto;
			margin: 0;
			border-radius: 0;
		}
	}

	section#how-does-it-work {
		background-image: url('{{ asset('images/wave-light.svg') }}');
		background-size: cover;
	}

	section#apis {
		background-image: url('{{ asset('images/wave-dark.svg') }}'),
						  url('{{ asset('images/glow.svg') }}'),
						  url('{{ asset('images/header-overlay.png') }}');
	}
@endpush

@push('footer')
	@include('includes.modals.track_info')
@endpush

@section('content')
	<header class="page-split master shadow-lg position-relative">
		<div class="container-fluid">
			<nav class="master d-flex justify-content-between align-items-center mb-10">
				<a href="#" class="logo">
					<img alt="Logo" src="{{ asset('images/logo-white.svg') }}" class="logo" />
				</a>
				@include('includes.user_menu')
			</nav>

			<h1 class="display-4 text-white fw-bold mb-1 animate__animated animate__fadeInDown animate__faster">Voluminous and quite simple</h1>
			<p class="lead text-white mb-10">Access open source internet data in a simple way with <strong class="fw-bold">{{ config('app.name') }}</strong></p>

			<div class="mb-5">
				@auth
					<a href="{{ route('dashboard') }}" class="btn btn-light shadow-sm px-4 rounded-pill">Dashboard</a>
				@else
					<a href="{{ route('user.gate') }}" class="btn btn-light shadow-sm px-4 rounded-pill">Start a free trial</a>
				@endif
			</div>
		</div>
		<a href="#what-is" class="d-flex align-items-center justify-content-center w-32px h-64px link-light position-absolute bottom-0">
			<i class="material-icons animate__animated animate__bounce animate__slow animate__infinite">arrow_downward</i>
		</a>
	</header>

	<section class="page-split position-relative" id="what-is">
		<div class="container-fluid">
			<h2 class="display-5 text-dark fw-bold mb-1">What is {{ config('app.name') }}?</h2>
			<p class="lead text-dark mb-10">Provides data to those who need <strong class="fw-bold">social media</strong> and <strong class="fw-bold">news</strong> data.</p>

			<span class="d-block mb-2">The platforms where we collect data for now</span>
			<div class="d-flex flex-wrap align-items-center gap-4 mb-4">
				@foreach (config('sources') as $key => $item)
				<div class="d-flex flex-column align-items-center gap-2">
					<img alt="{{ $item['name'] }}" src="{{ asset($item['icon']) }}" width="48" height="48" />
					<small>{{ $item['name'] }}</small>
				</div>
				@endforeach
			</div>
			<small class="d-block text-muted">We are working on more platforms.<br />You will receive free upgrades for all new resources.</small>
		</div>
		<a href="#how-does-it-work" class="d-flex align-items-center justify-content-center w-32px h-64px link-dark position-absolute bottom-0">
			<i class="material-icons animate__animated animate__bounce animate__slow animate__infinite">arrow_downward</i>
		</a>
	</section>

	<section class="page-split shadow-lg position-relative" id="how-does-it-work">
		<div class="container-fluid">
			<h2 class="display-5 text-dark fw-bold mb-5">How does it work?</h2>

			<div class="d-flex flex-column gap-5">
				<div>
					<h3 class="display-6 d-table w-64px h-64px shadow-lg bg-dark text-white fw-bold d-flex align-items-center justify-content-center rounded-circle mb-2">1</h3>
					<p class="lead mb-0">Each user specifies their own criteria</p>
				</div>
				<div>
					<div class="d-flex flex-wrap gap-5 mb-2">
						<div>
							<i class="material-icons icon-lg">face</i>
							Leila's criteria
							<div class="d-flex align-items-center gap-1">
								<small class="badge rounded-pill bg-dark fw-normal">+ Netflix</small>
								<small class="badge rounded-pill bg-dark fw-normal">+ Carrefour</small>
								<small class="badge rounded-pill bg-dark fw-normal">+ Tesla</small>
							</div>
						</div>
						<div>
							<i class="material-icons icon-lg">sentiment_satisfied_alt</i>
							Hector's criteria
							<div class="d-flex align-items-center gap-1">
								<small class="badge rounded-pill bg-dark fw-normal">+ Election</small>
								<small class="badge rounded-pill bg-dark fw-normal">+ Politics</small>
								<small class="badge rounded-pill bg-dark fw-normal">+ nytimes.com</small>
							</div>
						</div>
						<div>
							<i class="material-icons icon-lg">insert_emoticon</i>
							Paul's criteria
							<div class="d-flex align-items-center gap-1">
								<small class="badge rounded-pill bg-dark fw-normal">+ Coffe</small>
								<small class="badge rounded-pill bg-dark fw-normal">+ Arabica</small>
								<small class="badge rounded-pill bg-dark fw-normal">+ Starbucks</small>
							</div>
						</div>
					</div>
					<small class="text-muted">Everything is very simple, you only need to specify a <strong class="fw-bold">keyword</strong> or <strong class="fw-bold">domain</strong>.</small>
				</div>
				<div class="d-flex flex-column flex-md-row justify-content-between gap-5">
					<div>
						<h3 class="display-6 d-table w-64px h-64px shadow-lg bg-dark text-white fw-bold d-flex align-items-center justify-content-center rounded-circle mb-2">2</h3>
						<p class="lead mb-0">We start collecting data in line with these criteria</p>
					</div>
					<div>
						<h3 class="display-6 d-table w-64px h-64px shadow-lg bg-dark text-white fw-bold d-flex align-items-center justify-content-center rounded-circle mb-2">3</h3>
						<p class="lead mb-0">We collect the data we find in a common database</p>
						<small class="text-muted">You have 1 days to receive the data</small>
					</div>
				</div>
				<div>
					<h3 class="display-6 d-table w-64px h-64px shadow-lg bg-dark text-white fw-bold d-flex align-items-center justify-content-center rounded-circle mb-2">4</h3>
					<p class="lead mb-0">API access where you can provide unlimited queries in the entire database</p>
				</div>
			</div>
		</div>
		<a href="#example" class="d-flex align-items-center justify-content-center w-32px h-64px link-dark position-absolute bottom-0">
			<i class="material-icons animate__animated animate__bounce animate__slow animate__infinite">arrow_downward</i>
		</a>
	</section>

	<section class="page-split position-relative" id="example">
		<div class="container-fluid">
			<h2 class="display-5 text-dark fw-bold mb-1">Example Search API</h2>
			<p class="lead text-dark mb-10">For testing, inquire at <strong class="fw-bold">foxnews.com</strong></p>

			<div class="form-floating mb-4 mw-400px">
				<input
					type="text"
					class="form-control shadow-sm"
					name="search"
					id="search"
					value="biden"
                    data-blockui="html"
                    data-reset="true"
                    data-action="true"
                    data-action-target="#items" />
				<label for="search">Search news</label>
				<small class="text-muted"><span data-name="total">0</span> data found in the last 1 days</small>
			</div>

	        <div
	            id="items"
	            class="load row"
	            data-action="{{ route('index.search') }}"
	            data-callback="__results"
	            data-skip="0"
	            data-take="10"
	            data-include="search"
	            data-each="#items">
	            <div class="col-12 col-lg-6 each-model">
					<div class="card shadow-sm mb-4">
						<div class="card-body">
							<div class="row">
								<div class="col-12 col-sm-4 col-md-5">
									<img class="img-fluid rounded shadow-sm mb-2" data-col="image" alt="Image" />
								</div>
								<div class="col-12 col-sm-8 col-md-7">
									<h5 class="card-title">
										<a href="#" data-col="title" class="link-dark" target="_blank"></a>
									</h5>
								</div>
							</div>
							<small data-name="text" class="d-block h-100px overflow-auto mb-2"></small>
						</div>
					</div>
				</div>
	        </div>
	        <small class="text-muted">API Response</small>
			<pre class="mb-0 bg-grey rounded border border-1 p-2 shadow-sm h-400px overflow-auto" data-name="json"></pre>
		</div>
		<a href="#apis" class="d-flex align-items-center justify-content-center w-32px h-64px link-dark position-absolute bottom-0">
			<i class="material-icons animate__animated animate__bounce animate__slow animate__infinite">arrow_downward</i>
		</a>
	</section>

	<section class="page-split shadow-lg position-relative bg-dark" id="apis">
		<div class="container-fluid">
			<h2 class="display-5 text-dark fw-bold mb-1 text-white">Which API's will I access?</h2>
			<p class="small text-white mb-10 mw-768px">You can automate the criteria you will follow with Track Create, Track Delete and Track List API's. With the Search API, you can search the entire database according to the criteria you want.</p>

			<div class="row mb-5">
				<div class="col-6 col-sm-3">
					<h3 class="display-6 w-64px h-64px shadow-lg text-white fw-bold d-flex align-items-center justify-content-center rounded-circle mb-2">
						<i class="material-icons icon-lg">add</i>
					</h3>
					<div class="text-white fw-bold mb-5">Track Create API</div>
				</div>
				<div class="col-6 col-sm-3">
					<h3 class="display-6 w-64px h-64px shadow-lg text-white fw-bold d-flex align-items-center justify-content-center rounded-circle mb-2">
						<i class="material-icons icon-lg">clear</i>
					</h3>
					<div class="text-white fw-bold mb-5">Track Delete API</div>
				</div>
				<div class="col-6 col-sm-3">
					<h3 class="display-6 w-64px h-64px shadow-lg text-white fw-bold d-flex align-items-center justify-content-center rounded-circle mb-2">
						<i class="material-icons icon-lg">reorder</i>
					</h3>
					<div class="text-white fw-bold mb-5">Track List API</div>
				</div>
				<div class="col-6 col-sm-3">
					<h3 class="display-6 w-64px h-64px shadow-lg text-white fw-bold d-flex align-items-center justify-content-center rounded-circle mb-2">
						<i class="material-icons icon-lg">search</i>
					</h3>
					<div class="text-white fw-bold mb-5">Data Search API</div>
				</div>
			</div>
			<a
				href="#"
				class="btn btn-light shadow-sm px-4 rounded-pill"
				data-bs-toggle="modal"
				data-bs-target="#trackInfoModal">What is track?</a>
		</div>
		<a href="#pricing" class="d-flex align-items-center justify-content-center w-32px h-64px link-light position-absolute bottom-0">
			<i class="material-icons animate__animated animate__bounce animate__slow animate__infinite">arrow_downward</i>
		</a>
	</section>

	<section class="page-split position-relative" id="pricing">
		<div class="container-fluid">
			<h2 class="display-5 text-dark fw-bold mb-1">Pricing</h2>
			<p class="lead text-dark mb-10"><strong class="fw-bold">{{ config('app.name') }}</strong> is a price performance product</p>

			<div class="row row-cols-1 row-cols-md-3 align-items-center">
				@foreach ($plans as $key => $plan)
					<div class="col">
						<div class="card shadow-sm mb-4 {{ $key == 1 ? 'border-primary border-5' : '' }}">
							<div class="card-body d-flex flex-column gap-3 p-4">
								<h4 class="card-title mb-0">{{ $plan->name }}</h4>

								@if ($key == 1)
									<small class="p-1 text-center bg-light">Most Popular</small>
								@endif

								@if ($plan->price > 0)
									<div class="d-flex align-items-end gap-2">
										<span class="price fw-bold display-4">
											{{ config('cashier.currency_symbol') }}<span data-name="price">{{ intval($plan->price) }}</span>
										</span>
										<small class="text-muted h4">/Month</small>
									</div>
								@else
									<div class="d-flex align-items-end gap-2">
										<span class="price fw-bold display-4">Contact</span>
									</div>
								@endif
								<ul class="list-unstyled d-flex flex-column gap-2 mb-5 {{ $key == 1 ? 'pb-5' : '' }}">
									<li class="d-inline-flex align-items-center gap-2">
										<i class="material-icons text-success">check</i>
										@if ($track = $plan->track_limit)
											<span class="h6 mb-0">{{ $plan->track_limit }} tracks</span>
										@else
											<span class="h6 mb-0">Custom Tracks</span>
										@endif
									</li>
									<li class="d-inline-flex align-items-center gap-2">
										<i class="material-icons text-success">check</i>
										<span class="h6 mb-0">Limitless filterable query</span>
									</li>
									@if ($plan->price > 0)
										<li class="d-inline-flex align-items-center gap-2">
											<i class="material-icons text-success">check</i>
											<span class="h6 mb-0">E-mail support</span>
										</li>
									@else
										<li class="d-inline-flex align-items-center gap-2">
											<i class="material-icons text-success">check</i>
											<span class="h6 mb-0">Premium support</span>
										</li>
									@endif
								</ul>
								@if ($plan->price > 0)
									<a href="{{ route('user.gate') }}" class="btn btn-link shadow-none">Choose Plan</a>
								@else
									<a href="mailto:{{ config('etsetra.email') }}" class="btn btn-link shadow-none">Contact Us</a>
								@endif
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</section>

	<section class="page-split position-relative" id="need-more-information">
		<div class="container-fluid">
			<h2 class="display-5 text-dark fw-bold mb-1">Need more information?</h2>
			<p class="lead mb-10 mw-768px">If you would like to get more details or have additional questions, there's probably an answer already on our <a class="link-dark fw-bold text-decoration-underline" href="{{ route('faq.index') }}">Frequently Asked Questions</a> page.</p>

			<p class="lead mb-2">If you don't want to waste time</p>
			<a href="{{ route(auth()->check() ? 'dashboard' : 'user.gate') }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">Try it</a>
		</div>
	</section>

	@include('includes.footer')
@endsection

@push('js')
	function __results(__, obj)
	{
		$('[data-name=total]').html(obj.stats.total)
		$('[data-name=json]').html(JSON.stringify(obj, null, 2))
	}

	function __items(__, o)
	{
		__.find('[data-name=text]').html(app.nl2br(o.text))
		__.find('[data-col=title]').attr('href', '//' + o.link)
	}
@endpush
