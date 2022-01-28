@extends(
	'layouts.master',
	[
		'title' => 'Dashboard',
		'master' => true,
		'breadcrumb' => [
			'Dashboard' => route('dashboard'),
			'Account' => '#',
		]
	]
)

@push('js')
    function __secret_generated(__, obj)
    {
    	$('input#api_secret').val(obj.data.api_secret)
    }
@endpush

@section('content')
	<div class="card rounded-0 shadow-sm mb-4" id="accountCard">
		<div class="card-body">
			<div class="card-title text-uppercase h6 fw-bold">Account</div>
			<div class="d-flex align-items-center gap-3 mb-2">
				<div class="d-flex align-items-center gap-2">
					<img alt="Avatar" src="{{ auth()->user()->avatar }}" class="w-48px h-48px rounded-circle" />
					<div class="d-flex flex-column justify-content-center">
						<a title="GitHub" href="{{ 'https://github.com/'.auth()->user()->name }}" class="link-dark d-block h6 mb-0" target="_blank">{{ auth()->user()->name }}</a>
						<small class="text-muted">Github {{ __('validation.attributes.username') }}</small>
					</div>
				</div>
				<div class="d-flex flex-column">
					<span class="d-block h6 mb-0" target="_blank">{{ auth()->user()->email }}</span>
					<small class="text-muted">Github {{ __('validation.attributes.email') }}</small>
				</div>
			</div>
			<label class="form-check d-flex align-items-center gap-2 mb-0 px-0">
				<input
					class="form-check-input rounded-0 shadow-sm m-0"
					autocomplete="off"
					data-action="{{ route('user.email_alerts') }}"
					type="checkbox"
					name="email_alerts"
					{{ $emailAlerts ? 'checked' : '' }}
					value="on" />
				<small class="text-muted">I want the logs to be sent by e-mail</small>
			</label>
		</div>
	</div>
	<div class="card rounded-0 shadow-sm" id="tokens_card">
		<div class="card-body">
			<div class="card-title text-uppercase h6 fw-bold">Access Tokens</div>
			<div class="mb-2">
				<small class="text-muted">{{ __('validation.attributes.api_key') }}</small>
				<input readonly data-copy="api_key" data-copied="Api Key Copied!" class="form-control shadow-sm rounded-0" type="text" id="api_key" value="{{ auth()->user()->api_key }}" />
			</div>
			<div>
				<small class="text-muted">{{ __('validation.attributes.api_secret') }}</small>
				<div class="d-flex align-items-center gap-2">
					<input readonly data-copy="api_secret" data-copied="Api Secret Copied!" class="form-control shadow-sm rounded-0" type="text" id="api_secret" value="{{ auth()->user()->api_secret }}" />
					<a
						href="#"
						data-bs-toggle="tooltip"
						data-bs-placement="left"
						data-action="{{ route('user.api.secret_generator') }}"
						data-callback="__secret_generated"
						data-confirmation="Api Secret will change. Do you confirm?"
						title="Regenerate Api Secret"
						class="btn btn-outline-success rounded-0 shadow-sm">
						<i class="material-icons">refresh</i>
					</a>
				</div>
			</div>
		</div>
	</div>
@endsection
