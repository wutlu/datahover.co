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
    const driver = new app.Driver();

	function __subscription_details(__, obj)
	{
		let plan = obj.user.subscription.plan;

		$('[data-name=name]').html(plan.name)
		$('[data-name=balance]').html(obj.user.balance)

		$('input[name=plan][value=' + plan.id + ']').prop('checked', true)

		$('#planCard').addClass(plan.price > 0 ? 'border-success' : 'border-danger')
		$('[data-name=cancel-plan]').removeClass('d-none').addClass(plan.price > 0 ? '' : 'd-none')

		if (obj.last_invoice)
		{
			let form = $('form#paymentForm');
				form.find('input[name=amount]').val(obj.last_invoice.meta.amount)
				form.find('input[name=name]').val(obj.last_invoice.meta.name)
				form.find('select[name=country]').val(obj.last_invoice.meta.country).change()
				form.find('input[name=city]').val(obj.last_invoice.meta.city)
				form.find('input[name=zip_code]').val(obj.last_invoice.meta.zip_code)
				form.find('input[name=vat_id]').val(obj.last_invoice.meta.vat_id)
				form.find('input[name=phone]').val(obj.last_invoice.meta.phone)
				form.find('textarea[name=invoice_address]').val(obj.last_invoice.meta.invoice_address)
		}

		selectPlan()

		if (!obj.user.balance)
		{
            app.info('subscription.balance', function() {
                driver.highlight({
                    element: '[data-name=recharge]',
                    popover: {
                        title: 'Load balance first',
                        position: 'left',
                        showButtons: false,
                    }
                })
            })
		}
	}

	function __items(__, o)
	{
		__.find('.card').attr('id', 'Card' + o.id)
		__.find('[data-name=price]').html(parseInt(o.price))
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
	}).ready(function() {
        $('select[name=country]').select2(
            {
                dropdownParent: $('#recharge-modal'),
                theme: 'bootstrap-5'
            }
        );
    }).on('show.bs.modal','#recharge-modal', function () {
        driver.reset()
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
			data-action="{{ route('payment.order') }}"
			data-blockui="#recharge-modal->find(.modal-content)"
			id="paymentForm"
			autocomplete="off">
			<div class="d-flex align-items-center gap-2 mb-4">
				<img alt="Balance" src="{{ asset('images/payment.webp') }}" class="w-128px h-128px" />
				<div class="flex-fill">
					<div class="form-floating">
						<input type="number" class="form-control border-success rounded-0 shadow-sm text-success" id="amount" name="amount" value="10" min="10" max="100000" />
						<label for="amount">{{ __('validation.attributes.amount') }} * ({{ config('cashier.currency_symbol') }})</label>
						<small class="text-muted">The amount you want to charge</small>
						<small class="invalid-feedback"></small>
					</div>
				</div>
			</div>
			<h3 class="h6 fw-bold mb-2">Invoice Information</h3>
			<div class="form-floating mb-2">
				<input type="text" class="form-control rounded-0 shadow-sm" id="name" name="name" />
				<label for="name">{{ __('validation.attributes.name') }} * (Person or title to be invoiced)</label>
				<small class="invalid-feedback"></small>
			</div>
			<div class="form-floating mb-2">
				<textarea class="form-control rounded-0 shadow-sm" id="invoice_address" name="invoice_address"></textarea>
				<label for="invoice_address">{{ __('validation.attributes.invoice_address') }} *</label>
				<small class="invalid-feedback"></small>
			</div>
			<div class="d-flex align-items-start gap-2 mb-2">
				<div class="form-group w-50">
					<label class="small" for="country">{{ __('validation.attributes.country') }} *</label>
					<select class="form-select rounded-0 shadow-sm" name="country" id="country">
						@foreach (config('locale.countries') as $country)
							<option value="{{ $country }}" {{ $country == 'United Kingdom' ? 'selected' : '' }}>{{ $country }}</option>
						@endforeach
					</select>
					<small class="invalid-feedback"></small>
				</div>
				<div class="form-group w-50">
					<label class="small" for="city">{{ __('validation.attributes.city') }} *</label>
					<input type="text" class="form-control rounded-0 shadow-sm" id="city" name="city" />
					<small class="invalid-feedback"></small>
				</div>
			</div>
			<div class="row row-cols-1 row-cols-sm-3 align-items-start mb-2">
				<div class="col">
					<div class="form-group mb-2">
						<label class="small" for="zip_code">{{ __('validation.attributes.zip_code') }} *</label>
						<input type="text" class="form-control rounded-0 shadow-sm" id="zip_code" name="zip_code" />
						<small class="invalid-feedback"></small>
					</div>
				</div>
				<div class="col">
					<div class="form-group mb-2">
						<label class="small" for="phone">{{ __('validation.attributes.phone') }} *</label>
						<input type="text" class="form-control rounded-0 shadow-sm" id="phone" name="phone" />
						<small class="invalid-feedback"></small>
					</div>
				</div>
				<div class="col">
					<div class="form-group mb-2">
						<label class="small" for="vat_id">{{ __('validation.attributes.vat_id') }}</label>
						<input type="text" class="form-control rounded-0 shadow-sm" id="vat_id" name="vat_id" />
						<small class="invalid-feedback"></small>
					</div>
				</div>
			</div>
			<div class="form-check mb-4">
				<input class="form-check-input rounded-0 shadow-sm" type="checkbox" value="on" name="terms" id="terms" />
				<label class="form-check-label unselectable" for="terms">
					I have read and accept the <a href="{{ route('page', [ 'base' => 'legal', 'name' => 'privacy-policy' ]) }}" class="fw-bold link-dark" target="_blank">Privacy Policy</a> and <a href="{{ route('page', [ 'base' => 'legal', 'name' => 'terms-of-service' ]) }}" class="fw-bold link-dark" target="_blank">Terms of Service</a>
				</label>
				<small class="invalid-feedback"></small>
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
									<small>Balance {{ config('cashier.currency_symbol') }}<span data-name="balance">0</span></small>
								</label>
								<a
									href="#"
									class="btn btn-outline-success rounded-0 shadow-sm"
									data-bs-toggle="modal"
									data-bs-target="#recharge-modal"
									data-name="recharge">
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
							{{ config('cashier.currency_symbol') }}<span data-name="price">-</span>
						</span>
						<small class="text-muted">/ Month</small>
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
							<span class="h6 mb-0">Limitless xml or json feed</span>
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
						<input type="radio" name="plan" data-alias="id" data-col="id" />
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="text-center py-5">
		<p class="text-muted mb-0">You can contact us via live support or <a class="link-primary" href="mailto:{{ config('etsetra.email') }}">{{ config('etsetra.email') }}</a> mail for special plans.</p>
	</div>
@endsection
