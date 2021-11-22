@extends(
	'layouts.master',
	[
		'title' => 'Dashboard',
		'master' => true,
		'breadcrumb' => [
			'Dashboard' => route('dashboard')
		]
	]
)

@push('js')
	let __items = function(__, o)
	{
		__.find('[data-name=link]').attr('href', '//' + o.site)
	}

	$(document).ready(function() {
		@if (!$greetingWelcome)
			let modal = $('#greetingModal').modal('show');
		@endif
	})
@endpush

@push('footer')
	<div
		id="greetingModal"
		class="modal fade"
		aria-hidden="true"
		aria-labelledby="greetingModalLabel"
		tabindex="-1"
		data-bs-backdrop="static"
		data-bs-keyboard="false">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content shadow border-0 rounded-0">
				<div class="modal-header border-0">
					<h5 class="modal-title">Welcome</h5>
					<a
						href="#"
						class="link-dark"
						data-bs-target="#carouselExampleDark"
						data-bs-slide="next">
						<i class="material-icons">arrow_forward_ios</i>
					</a>
				</div>
				<div class="modal-body">
					<div id="carouselExampleDark" class="carousel carousel-dark" data-bs-ride="carousel" data-bs-wrap="false" data-bs-interval="false">
						<div class="carousel-indicators">
							<button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="h-16px active"></button>
							<button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1" class="h-16px"></button>
							<button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2" class="h-16px"></button>
						</div>
						<div class="carousel-inner p-0">
							<div class="carousel-item h-200px active">
								<div class="carousel-caption top-0">
									<h5>Specify Track</h5>
									<p class="text-muted">We will scan the internet for you. However, you need to specify criteria for us.</p>
								</div>
							</div>
							<div class="carousel-item h-200px">
								<div class="carousel-caption top-0">
									<h5>Connect to Our API's</h5>
									<p class="text-muted">Use our Search API. Get all the content on your server according to the criteria you want.</p>
								</div>
							</div>
							<div class="carousel-item h-256px">
								<div class="carousel-caption top-0">
									<h5>Automation</h5>
									<p class="text-muted">Automate your own with our track API's!</p>
									<a href="#" class="btn btn-outline-secondary rounded-0" data-bs-dismiss="modal">Enter dashboard</a>
								</div>
							</div>
						</div>
					</div>

					<div class="d-flex justify-content-end">
						<label class="form-check d-flex align-items-center gap-2">
							<input
								class="form-check-input rounded-0 shadow-sm"
								autocomplete="off"
								data-action="{{ route('user.info.hide') }}"
								type="checkbox"
								name="info_key"
								value="greeting.welcome" />
							<small>Don't show it again</small>
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>
@endpush

@section('content')
	<div class="card rounded-0 shadow-sm" id="masterCard">
		<div class="card-body">
			<div class="d-flex flex-column flex-lg-row">
				<div class="d-flex gap-2 me-auto mb-1">
					<span class="card-title text-uppercase h6 fw-bold mb-0">Log Console</span>
				</div>
				<div class="d-flex gap-1">
					<input
						type="text"
						class="form-control form-control-sm shadow-sm rounded-0 bg-light px-3"
						name="search"
						data-blockui="#masterCard"
						data-reset="true"
						data-action="true"
						data-action-target="#items"
						placeholder="Search" />
				</div>
			</div>
		</div>
		<div class="alert alert-info rounded-0 shadow-sm d-flex align-items-center gap-2 border-start-0 border-end-0 mb-0">
			<i class="material-icons">info</i>
			The negative results that occur while processing the tracks you define are listed here.
		</div>
		<ul
			id="items"
			class="list-group list-group-flush load border-0"
			data-action="{{ route('api.logs.list') }}"
			data-skip="0"
			data-take="5"
			data-include="search"
			data-loading="#items->children(.loading)"
			data-headers='{"X-Api-Key": "{{ auth()->user()->api_key }}", "X-Api-Secret": "{{ auth()->user()->api_secret }}"}'
			data-more="#itemsMore"
			data-each="#items">
			<li class="list-group-item border-0 d-flex justify-content-center loading">
				<img alt="Loading" src="{{ asset('images/rolling-dark.svg') }}" class="w-32px h-32px" />
			</li>
			<li class="list-group-item border-0 each-model">
				<div class="d-flex justify-content-between">
					<a href="#" target="_blank" class="link-primary d-flex flex-column" data-name="link">
						<span data-col="site" class="fw-bold"></span>
						<small data-col="created_at" class="text-muted"></small>
					</a>
					<small class="d-flex flex-column align-items-end text-muted">
						<span data-col="repeat"></span> repeat
					</small>
				</div>
				<small class="d-block text-muted bg-light p-1" data-col="message"></small>
			</li>
		</ul>
		<a
			href="#"
			id="itemsMore"
			class="d-none py-1"
			data-blockui="#masterCard"
			data-action="true"
			data-action-target="#items">
			<i class="material-icons d-table mx-auto text-muted">more_horiz</i>
		</a>
		<div class="accordion" id="apiAccordion">
			@foreach ($apis as $key => $api)
				<div class="accordion-item border-0 rounded-0 border-top">
					<div class="accordion-header">
						<button
							class="accordion-button bg-light rounded-0 shadow-none collapsed py-2"
							type="button"
							data-bs-toggle="collapse"
							data-bs-target="#{{ $key }}">
							<small class="text-muted">{{ $api['name'] }}</small>
						</button>
					</div>
					<div id="{{ $key }}" class="accordion-collapse collapse border-0" data-bs-parent="#apiAccordion">
						<ul class="list-group list-group-flush">
							<li class="list-group-item ps-4 d-flex flex-column border-0">
								<small class="text-muted">{{ $api['method'] }}</small>
								<label>{{ $api['route'] }}</label>
							</li>
							<li class="list-group-item ps-4 d-flex flex-column border-0">
								<small class="text-muted">Request Limit</small>
								<label>{{ $rate_limit }} request per {{ $rate_minutes }} minutes</label>
							</li>
							<li class="list-group-item fw-bold border-0 text-uppercase">Headers</li>
							<li class="list-group-item ps-4 d-flex flex-column border-0 py-1">
								<small class="text-muted">X-Api-Key</small>
								<label>{{ auth()->user()->api_key }}</label>
							</li>
							<li class="list-group-item ps-4 d-flex flex-column border-0 py-1">
								<small class="text-muted">X-Secret-Key</small>
								<label>{{ auth()->user()->api_secret }}</label>
							</li>
							<li class="list-group-item ps-4 d-flex flex-column border-0 py-1">
								<small class="text-muted">Accept</small>
								<label>application/json</label>
							</li>

							<li class="list-group-item fw-bold border-0 text-uppercase">Params</li>

							@foreach ($api['params'] as $key => $value)
								<li class="list-group-item ps-4 d-flex flex-column border-0 py-1">
									<small class="text-muted">{{ $key }}</small>
									<label>{{ $value }}</label>
								</li>
							@endforeach
						</ul>
					</div>
				</div>
			@endforeach
		</div>
	</div>
@endsection
