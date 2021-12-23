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

@push('js_hide')
	$(document).ready(function() {
		@if (!$greetingWelcome)
			let modal = $('#greetingModal').modal('show');
		@endif

		$('#greetingCarousel').on('slide.bs.carousel', function (e) {
			let trigger = $('[data-bs-target="#greetingCarousel"]');

			if (e.to == 2)
				trigger.addClass('d-none')
			else
				trigger.removeClass('d-none')
		})
	})
@endpush

@push('css')

@endpush

@push('footer')
<!-- 	<div
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
					<h5 class="modal-title unselectable">Welcome</h5>
					<a
						href="#"
						class="link-dark"
						data-bs-target="#greetingCarousel"
						data-bs-slide="next">
						<i class="material-icons">arrow_forward_ios</i>
					</a>
				</div>
				<div class="modal-body">


					<div class="d-flex justify-content-end">
						<label class="form-check d-flex align-items-center gap-2 p-0">
							<small>Don't show it again</small>
							<input
								class="form-check-input rounded-0 shadow-sm m-0"
								autocomplete="off"
								data-action="{{ route('user.hide_info') }}"
								type="checkbox"
								name="info_key"
								value="greeting.welcome" />
						</label>
					</div>
				</div>
			</div>
		</div>
	</div> -->
@endpush

@section('content')
	<div class="card bg-transparent border-0 rounded-0 mb-4">
		<div id="greetingCarousel" class="carousel carousel-dark" data-bs-ride="carousel" data-bs-wrap="false" data-bs-interval="false">
			<div class="carousel-indicators">
				<button class="active" type="button" data-bs-target="#greetingCarousel" data-bs-slide-to="0"></button>
				<button type="button" data-bs-target="#greetingCarousel" data-bs-slide-to="1"></button>
				<button type="button" data-bs-target="#greetingCarousel" data-bs-slide-to="2"></button>
			</div>
			<div class="carousel-inner">
				<div class="carousel-item h-200px active">
					<div class="carousel-caption d-flex flex-column justify-content-center top-0 bottom-0">
						<h5 class="fw-bold text-uppercase">Specify Track</h5>
						<p class="text-muted">We will scan the internet for you. However, you need to specify criteria for us.</p>
					</div>
				</div>
				<div class="carousel-item h-200px">
					<div class="carousel-caption d-flex flex-column justify-content-center top-0 bottom-0">
						<h5 class="fw-bold text-uppercase">Connect to Our API's</h5>
						<p class="text-muted">Use our Search API. Get all the content on your server according to the criteria you want.</p>
					</div>
				</div>
				<div class="carousel-item h-200px">
					<div class="carousel-caption d-flex flex-column justify-content-center top-0 bottom-0">
						<h5 class="fw-bold text-uppercase">Automation</h5>
						<p class="text-muted">Automate your own with our track API's!</p>
					</div>
				</div>
			</div>
			<button class="carousel-control-prev" type="button" data-bs-target="#greetingCarousel" data-bs-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Previous</span>
			</button>
			<button class="carousel-control-next" type="button" data-bs-target="#greetingCarousel" data-bs-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Next</span>
			</button>
		</div>
	</div>
	<div class="row">
		<div class="col-6 col-xl-3">
			<div class="card card-coverage border-0 mb-4 bg-transparent">
				<div class="card-body h-200px d-flex rounded flex-column justify-content-between bg-transparent">
					<span class="h4 fw-bold mb-0">Best social data technology</span>
					<small class="h6 fw-bold mb-0">{{ config('app.name') }}</small>
				</div>
			</div>
		</div>
		<div class="col-6 col-xl-3">
			<div class="card card-coverage border-0 shadow-sm mb-4" style="background-image: url('{{ asset('images/news.jpg') }}');">
				<div class="card-body h-200px d-flex rounded flex-column justify-content-between">
					<small class="h6 fw-bold text-white mb-0">NEWS</small>
					<span class="h4 fw-bold text-white mb-0">Track news sites</span>
				</div>
			</div>
		</div>
		<div class="col-6 col-xl-3">
			<div class="card card-coverage border-0 shadow-sm mb-4" style="background-image: url('{{ asset('images/social.jpg') }}');">
				<div class="card-body h-200px d-flex rounded flex-column justify-content-between">
					<small class="h6 fw-bold text-white mb-0">SOCIAL</small>
					<span class="h4 fw-bold text-white mb-0">We are working on more resources</span>
				</div>
			</div>
		</div>
		<div class="col-6 col-xl-3">
			<div class="card card-coverage border-0 shadow-sm mb-4" style="background-image: url('{{ asset('images/stats.jpg') }}');">
				<div class="card-body h-200px d-flex rounded flex-column justify-content-between">
					<small class="h6 fw-bold text-white mb-0">STATISTICS</small>
					<span class="h4 fw-bold text-white mb-0">Big data for detailed statistics</span>
				</div>
			</div>
		</div>
	</div>
@endsection
