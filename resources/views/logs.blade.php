@extends(
	'layouts.master',
	[
		'title' => 'Logs',
		'master' => true,
		'breadcrumb' => [
			'Dashboard' => route('dashboard'),
			'Log Console' => '#'
		]
	]
)

@push('js')
	let __items = function(__, o)
	{
		__.find('[data-name=link]')
	}
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
			data-action="{{ route('user.logs.list') }}"
			data-skip="0"
			data-take="50"
			data-include="search"
			data-loading="#items->children(.loading)"
			data-more="#itemsMore"
			data-each="#items">
			<li class="list-group-item border-0 d-flex justify-content-center loading">
				<img alt="Loading" src="{{ asset('images/rolling-dark.svg') }}" class="w-32px h-32px" />
			</li>
			<li class="list-group-item border-0 each-model">
				<div class="d-flex align-items-start justify-content-between">
					<div data-name="link">
						<small data-col="site" class="border border-1 rounded px-1 shadow-sm text-muted bg-light"></small>
						<small class="text-dark" data-col="message"></small>
						<small data-col="updated_at" class="text-muted"></small>
					</div>
					<small class="d-flex gap-1 align-items-end text-muted">
						<span data-col="repeat"></span> repeat
					</small>
				</div>
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
	</div>
	<div class="d-flex justify-content-end">
		<label class="form-check d-flex align-items-center gap-2 py-2 px-0">
			<small class="text-muted">I want the logs to be sent by e-mail</small>
			<input
				class="form-check-input rounded-0 shadow-sm m-0"
				autocomplete="off"
				data-action="{{ route('user.email_alerts') }}"
				type="checkbox"
				name="email_alerts"
				{{ $emailAlerts ? 'checked' : '' }}
				value="on" />
		</label>
	</div>
@endsection
