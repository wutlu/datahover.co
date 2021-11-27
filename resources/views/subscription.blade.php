@extends(
	'layouts.master',
	[
		'title' => 'Manage Subscription',
		'master' => true,
		'breadcrumb' => [
			'Dashboard' => route('dashboard'),
			'Manage Subscription' => '#',
		]
	]
)

@push('css')
	.plan-card:hover {
		border-color: #d6bc6f;
		border-width: 3px;
	}
@endpush

@push('js')
	function __subscription_details(__, obj)
	{
		let plan = obj.user.subscription.plan;

		$('[data-name=name]').html(plan.name)
		$('[data-name=balance]').html(obj.user.balance)

		$('input[name=plan][value=' + plan.key + ']').prop('checked', true)

		$('#planCard').addClass(plan.key == 'trial' ? 'border-danger' : 'border-success')
		$('[data-name=cancel-plan]').removeClass('d-none').addClass(plan.key == 'trial' ? 'd-none' : '')

		selectPlan()
	}

	function __items(__, o)
	{
		__.find('.card').attr('id', o.key + 'Card')
	}

	function selectPlan()
	{
		let radios = $('input[name=plan][value]');

		$.each(radios, function(k, __) {
			__ = $(__);
			let card = __.closest('.card');

			if (__.is(':checked'))
				card.addClass('border-primary')
			else
				card.removeClass('border-primary')
		})
	}

	$(document).on('click', '.plan-card', function() {
		$(this).find('input[name=plan]').prop('checked', true)

		selectPlan()
	})

	$(window).on('load', function() {
		$('#recharge-modal').modal('show')
	})
@endpush

@push('footer')
	@component('includes.modals.modal')
		@slot('title', 'Load Balance')
		@slot('name', 'recharge')
		@slot('backdrop', 'static')
		@slot('keyboard', 'false')
		<form
			method="post"
			action="#"
			data-action="{{ route('subscription.order') }}"
			data-blockui="#recharge-modal->find(.modal-content)"
			id="paymentForm">
			<div class="d-flex align-items-center gap-2 mb-4">
				<img alt="Balance" src="{{ asset('images/payment.webp') }}" class="w-128px h-128px" />
				<div class="flex-fill">
					<div class="form-floating">
						<input type="number" class="form-control border-success rounded-0 shadow-sm text-success" id="amount" name="amount" value="{{ config('plans.basic.price') }}" min="10" max="100000" />
						<label for="amount">Amount * ($)</label>
						<small class="text-muted">The amount you want to charge</small>
					</div>
				</div>
			</div>
			<h3 class="h6 fw-bold mb-2">Invoice Information</h3>
			<div class="form-floating mb-2">
				<input type="text" class="form-control rounded-0 shadow-sm" id="name" name="name" />
				<label for="name">Name * (Person or title to be invoiced)</label>
			</div>
			<div class="d-flex align-items-center gap-2 mb-2">
				<div class="form-floating">
					<select class="form-select rounded-0 shadow-sm" name="country" id="country">
						<option selected>Country</option>
						@foreach (config('locale.countries') as $country)
							<option value="{{ $country }}">{{ $country }}</option>
						@endforeach
					</select>
					<label for="country">Country *</label>
				</div>
				<div class="form-floating">
					<input type="text" class="form-control rounded-0 shadow-sm" id="city" name="city" />
					<label for="city">City *</label>
				</div>
				<div class="form-floating">
					<input type="text" class="form-control rounded-0 shadow-sm" id="zip_code" name="zip_code" />
					<label for="zip_code">Zip Code *</label>
				</div>
			</div>
			<div class="d-flex align-items-center gap-2 mb-2">
				<div class="form-floating w-50">
					<input type="text" class="form-control rounded-0 shadow-sm" id="vat_id" name="vat_id" />
					<label for="vat_id">Vat Id</label>
				</div>
				<div class="form-floating w-50">
					<input type="text" class="form-control rounded-0 shadow-sm" id="phone" name="phone" />
					<label for="phone">Phone *</label>
				</div>
			</div>
			<div class="form-floating mb-2">
				<textarea class="form-control rounded-0 shadow-sm" id="invoice_address" name="invoice_address"></textarea>
				<label for="invoice_address">Invoice Address *</label>
			</div>
			<div class="form-check mb-4">
				<input class="form-check-input rounded-0 shadow-sm" type="checkbox" value="on" name="terms" id="terms" />
				<label class="form-check-label" for="terms">
					I have read and accept the <a href="{{ route('page', [ 'base' => 'legal', 'name' => 'privacy-policy' ]) }}" class="fw-bold link-dark" target="_blank">Privacy Policy</a> and <a href="{{ route('page', [ 'base' => 'legal', 'name' => 'terms-of-service' ]) }}" class="fw-bold link-dark" target="_blank">Terms of Service</a>
				</label>
			</div>

			<div class="d-flex align-items-center gap-2 mt-5">
				<img alt="mastercard" src="{{ asset('images/mastercard.svg') }}" class="w-auto h-24px" />
				<img alt="visa" src="{{ asset('images/visa.svg') }}" class="w-auto h-24px" />
				<div class="d-flex align-items-center gap-2 px-1">
					<img alt="protected" src="{{ asset('images/protected.svg') }}" class="w-auto h-24px grayscale opacity-75" />
					<small class="d-flex flex-column">
						<small class="fw-bold">Privacy</small>
						<small>Protected</small>
					</small>
				</div>
				<button type="submit" class="btn btn-outline-success rounded-0 shadow-sm ms-auto">Proceed to Payment</button>
			</div>
		</form>
	@endcomponent
@endpush

@section('content')
	<div
		id="items"
		class="row load"
		data-action="{{ route('subscription.details') }}"
		data-callback="__subscription_details"
		data-each="#items">
		<div class="col-12">
			<div id="planCard" class="card border-1 rounded-0 shadow-sm border mb-4">
				<div class="card-body">
					<div class="d-flex justify-content-between">
						<div class="d-flex flex-column justify-content-center">
							<span>
								<span class="fw-bold" data-name="name">-</span> (Current)
							</span>
							<a
								href="#"
								class="small link-danger d-none"
								data-name="cancel-plan"
								data-action="{{ route('subscription.cancel') }}"
								data-blockui="#planCard"
								data-confirmation="If you cancel the plan, the remaining time will be calculated and added to your balance. 1 day commission is charged for cancellation. Do you confirm?">Cancel Plan</a>
						</div>
						<div class="d-flex flex-column text-end">
							<div class="input-group shadow-sm flex-nowrap">
								<label class="input-group-text rounded-0">
									<small>Balance $<span data-name="balance">0</span></small>
								</label>
								<a
									href="#"
									class="btn btn-outline-success rounded-0 shadow-sm"
									data-bs-toggle="modal"
									data-bs-target="#recharge-modal">
									<i class="material-icons" data-bs-toggle="tooltip" data-bs-placement="left" title="Load Balance">add</i>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-12 col-lg-4 each-model">
			<div class="card plan-card border-1 rounded-0 shadow-sm mb-4 position-relative">
				<div class="card-body d-flex flex-column gap-3 p-4">
					<h4 class="card-title mb-0" data-col="name">-</h4>
					<div class="d-flex align-items-end gap-2">
						<span class="price fw-bold display-5">
							$<span data-col="price">-</span>
						</span>
						<small class="text-muted h5">/Month</small>
					</div>
					<ul class="list-unstyled d-flex flex-column gap-2">
						<li class="d-inline-flex align-items-center gap-2">
							<i class="material-icons text-success">check</i>
							<span class="h6 mb-0"><span data-col="track_limit"></span> track</span>
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
					<a
						href="#"
						data-action="{{ route('subscription.start') }}"
						data-include="plan"
						data-confirmation="If your account has a package history, as many tracks as supported by your chosen subscription will be accepted. Any excess will be removed from your account. Do you want to start a subscription for your chosen plan?"
						class="stretched-link text-center">Select Plan</a>
					<div class="d-none">
						<input type="radio" name="plan" data-col="key" />
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
