@extends(
	'layouts.master',
	[
		'title' => 'Dashboard',
		'master' => true,
		'breadcrumb' => [
			'Dashboard' => route('dashboard'),
			'Subscription' => '#',
		]
	]
)

@push('js')
    function __secret_generated(__, obj)
    {
    	$('input#api_secret').val(obj.data.api_secret)
    }

    $(document).on('change', 'input[name=months]', priceCalculate)

	let months = $('input[name=months]');
	let price = $('input[name=price]');
	let total = $('input[name=total]');

    function __subscription(__, obj)
    {
    	price.val(obj.data.price)

    	$('[data-name=name]').html(obj.data.name)
    	$('[data-name=price]').html(obj.data.price)
    	$('[data-name=track_limit]').html(obj.data.track_limit)

    	priceCalculate()
    }

    function priceCalculate()
    {
    	total.val(months.val() * price.val())

    	$('[data-name=total]').html(total.val())
    }
@endpush

@section('content')
	<div class="alert alert-success rounded-0 shadow-sm mb-4">
		<div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
			<div class="d-flex align-items-center gap-2">
				<i class="material-icons">info</i>
				You currently have a subscription.
			</div>
			<div class="d-flex flex-column">
				{{-- {{ $subscription->package['name'] }} --}}
				{{ config('app.name') }} Monthly Plan
				<small class="text-danger">{{ $subscription->days }} days left</small>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12 col-lg-4">
			<div
				id="subscription"
				class="card rounded-0 shadow mb-4 border-3 border-success mb-4 load"
				data-action="{{ route('user.get_subscription') }}"
				data-callback="__subscription"
				data-include="name">
				<div class="card-body d-flex flex-column gap-3 p-4">
					<div class="form-floating">
						<select
							class="form-select shadow-sm rounded-0 pe-4 bg-light"
							name="name"
							autocomplete="off"
							data-action="true"
							data-action-target="#subscription"
							data-blockui="#subscription">
							@foreach ($subscriptions as $key => $item)
								<option value="{{ $key }}" {{ $key == auth()->user()->subscription ? 'selected' : '' }}>
									{{ $item['name'] }}
									{{ $key == auth()->user()->subscription ? '(Current)' : '' }}
								</option>
							@endforeach
						</select>
						<label for="name">Plan</label>
					</div>
					<div class="d-flex align-items-end gap-2">
						<span class="price fw-bold display-4">
							$<span data-name="price">-</span>
						</span>
						<small class="text-muted h4">/Month</small>
					</div>
					<ul class="list-unstyled d-flex flex-column gap-2 mb-0">
						<li class="d-inline-flex align-items-center gap-2">
							<i class="material-icons text-success">check</i>
							<span class="h6 mb-0"><span data-name="track_limit">-</span> track</span>
						</li>
						<li class="d-inline-flex align-items-center gap-2">
							<i class="material-icons text-success">check</i>
							<span class="h6 mb-0">Limitless query request</span>
						</li>
						<li class="d-inline-flex align-items-center gap-2">
							<i class="material-icons text-success">check</i>
							<span class="h6 mb-0">E-mail support</span>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-12 col-lg-8">
			<div class="card rounded-0 shadow mb-4">
				<div class="card-body">
					<input type="hidden" value="0" name="price" />
					<input type="hidden" value="0" name="total" />
					<ul class="list-group list-group-flush mb-4">
						<li class="list-group-item px-0">
							<div class="d-flex justify-content-between align-items-center">
								<span class="h5 mb-0">Order Summary</span>
								<i class="material-icons text-danger">keyboard_arrow_left</i>
							</div>
						</li>
						<li class="list-group-item px-0">
							<div class="row">
								<div class="col-3">
									<div class="d-flex flex-column">
										<span class="fw-bold" data-name="name">-</span>
										<small class="text-muted">Plan</small>
									</div>
								</div>
								<div class="col-5">
									<div class="form-floating">
										<input type="number" class="form-control rounded-0 shadow-sm" name="months" min="1" max="12" value="1" />
										<label for="months">Months</label>
									</div>
								</div>
								<div class="col-4">
									<div class="d-flex flex-column text-end">
										<span class="fw-bold">$<span data-name="total">0</span>.00</span>
										<small class="text-muted">Total</small>
									</div>
								</div>
							</div>
						</li>
					</ul>
					<small class="d-block text-muted mb-2 mw-400px">You will be redirected to PayPal for payment. If successful, your subscription will be defined as successful.</small>
					<a href="#" class="btn btn-dark py-3 rounded-0 shadow-sm d-flex align-items-center justify-content-center gap-2">
						<img alt="PayPal" src="{{ asset('images/paypal.svg') }}" class="w-16px h-16px" />
						Continue With PayPal
					</a>
				</div>
			</div>
		</div>
	</div>
@endsection
