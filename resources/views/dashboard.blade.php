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
		let modal = $('#greetingModal').modal();
			modal.modal('show')
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
									<h5>First slide label</h5>
									<p class="text-muted">Some representative placeholder content for the first slide.</p>
								</div>
							</div>
							<div class="carousel-item h-200px">
								<div class="carousel-caption top-0">
									<h5>Second slide label</h5>
									<p class="text-muted">Some representative placeholder content for the second slide.</p>
								</div>
							</div>
							<div class="carousel-item h-200px">
								<div class="carousel-caption top-0">
									<h5>Third slide label</h5>
									<p class="text-muted">Some representative placeholder content for the third slide.</p>
									<a href="#" class="btn btn-outline-secondary rounded-0" data-bs-dismiss="modal">Enter dashboard</a>
								</div>
							</div>
						</div>
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
		<div class="card-body">
			<ul
				id="items"
				class="list-group list-group-flush load"
				data-action="{{ route('logs') }}"
				data-skip="0"
				data-take="10"
				data-include="search"
				data-loading="#items->children(.loading)"
				data-more="#items_more"
				data-each="#items">
				<li class="list-group-item border-0 d-flex justify-content-center loading">
					<img alt="Loading" src="{{ asset('images/rolling-dark.svg') }}" class="w-32px h-32px" />
				</li>
				<li class="list-group-item border-0 each-model">
					<div class="d-flex justify-content-between">
						<a href="#" target="_blank" class="link-primary fw-bold" data-name="link">
							<span data-col="site"></span>
							<p class="text-muted mb-0" data-col="message"></p>
						</a>
						<div class="d-flex flex-column align-items-end">
							<small data-col="created_at" class="text-muted"></small>
							<small class="text-muted">
								<span data-col="repeat"></span> repeat
							</small>
						</div>
					</div>
				</li>
			</ul>
		</div>
		<a
			href="#"
			id="items_more"
			class="d-none py-1"
			data-blockui="#masterCard"
			data-action="true"
			data-action-target="#items">
			<i class="material-icons d-table mx-auto text-muted">more_horiz</i>
		</a>
	</div>
@endsection
