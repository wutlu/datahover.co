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
	.plan-card.border-primary .stretched-link {
		visibility: hidden;
	}
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
						<input type="number" class="form-control rounded-0 shadow-sm" id="amount" name="amount" value="{{ config('plans.basic.price') }}" min="10" max="100000" />
						<label for="amount">Amount ($)</label>
						<small class="text-muted">The amount you want to charge</small>
					</div>
				</div>
			</div>

			<div class="d-flex align-items-center gap-2">
				<img src="{{ asset('images/mastercard.svg') }}" class="w-auto h-24px grayscale opacity-75" />
				<img src="{{ asset('images/visa.svg') }}" class="w-auto h-24px grayscale opacity-75" />
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
			<div id="planCard" class="card border-3 rounded-0 shadow-sm border mb-4">
				<div class="card-body">
					<div class="d-flex justify-content-between">
						<div class="d-flex flex-column">
							<span>
								<span class="fw-bold" data-name="name">-</span> (Current)
							</span>
							<a
								href="#"
								class="small link-danger"
								data-action="{{ route('subscription.cancel') }}"
								data-blockui="#planCard"
								data-confirmation="If you cancel the plan, the remaining time will be calculated and added to your balance. You cannot get this balance refunded. Do you confirm?">Cancel Plan</a>
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
			<div class="card plan-card border-3 rounded-0 shadow-sm mb-4 position-relative">
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
						data-confirmation="Do you want to start a subscription for your chosen plan?"
						class="stretched-link text-center">Select Plan</a>
					<div class="d-none">
						<input type="radio" name="plan" data-col="key" />
					</div>
				</div>
			</div>
        </div>
	</div>
@endsection
