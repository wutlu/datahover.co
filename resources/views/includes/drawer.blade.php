@php
$subscription = auth()->user()->subscription();
@endphp

@push('js')
	let ds = '.drawer-subscription';
	let ph = $(ds).children('.payments');

	$(document).on('mouseover', ds, function() {
		ph.slideDown(100)
	}).on('mouseleave', '.drawer-subscription', function() {
		ph.slideUp(100)
	})
@endpush

<aside class="drawer">
	<div class="card shadow-sm position-initial border-sm-1 rounded-0 shadow-sm">
		<div class="card-body d-flex flex-column gap-3">
			<div class="d-flex flex-column flex-xl-row justify-content-between align-xl-items-center">
				<h6 class="card-title fw-bold mb-0">{{ $subscription->plan->name }}</h6>
				<small class="text-muted">{{ $subscription->days }} days left</small>
			</div>

			@if ($subscription->plan->price > 0)
				@if ($subscription->days == 0)
					<div class="alert alert-danger border border-1 border-danger rounded-0 shadow-sm small mb-0">Your subscription has expired!</div>
				@elseif ($subscription->days <= 7)
					<div class="alert alert-warning border border-1 border-warning rounded-0 shadow-sm small mb-0">Expires on {{ date('M d\t\h Y', strtotime($subscription->end_date)) }}</div>
				@endif
			@else
				<small class="text-muted rounded-0 mb-0 d-flex align-items-center gap-3">
					<i class="material-icons">warning</i>
					You are using a trial subscription. For a better service, please choose a package.
				</small>
			@endif

			<div class="d-flex flex-column gap-1 drawer-subscription">
				<a href="{{ route('subscription.index') }}" class="btn btn-sm btn-outline-primary d-block rounded-0 shadow-sm">Manage Subscription</a>
				<a href="{{ route('payments') }}" class="mx-auto small text-muted payments" style="display: none;">Payments</a>
			</div>
		</div>
		<div class="card-body">
			<small class="card-title text-muted text-uppercase mb-0">Menu</small>
		</div>
		<div class="list-group list-group-flush rounded-0">
			<a href="{{ route('track.dashboard') }}" class="list-group-item small list-group-item-action d-flex align-items-center gap-2 link-dark">
				<i class="material-icons">scatter_plot</i>
				Track List
			</a>
			<a href="{{ route('search.dashboard') }}" class="list-group-item small list-group-item-action d-flex align-items-center gap-2 link-dark">
				<i class="material-icons">search</i>
				Search Api
			</a>
			<a href="{{ route('logs') }}" class="list-group-item small list-group-item-action d-flex align-items-center gap-2 link-dark">
				<i class="material-icons">logo_dev</i>
				Log Console
			</a>
		</div>
		@if (auth()->user()->is_root)
			<div class="card-body">
				<small class="card-title text-muted text-uppercase mb-0">ROOT Menu</small>
			</div>
			<div class="list-group list-group-flush rounded-0">
				<a href="{{ route('root.users') }}" class="list-group-item small list-group-item-action d-flex align-items-center gap-2 link-dark">
					<i class="material-icons">people</i>
					User Management
				</a>
				<a href="{{ route('root.plans') }}" class="list-group-item small list-group-item-action d-flex align-items-center gap-2 link-dark">
					<i class="material-icons">language</i>
					Plan Management
				</a>
				<a href="{{ route('root.tracks') }}" class="list-group-item small list-group-item-action d-flex align-items-center gap-2 link-dark">
					<i class="material-icons">scatter_plot</i>
					Track Management
				</a>
				<a href="{{ route('root.proxies') }}" class="list-group-item small list-group-item-action d-flex align-items-center gap-2 link-dark">
					<i class="material-icons">filter_4</i>
					Proxy Management
				</a>
				<a href="{{ route('root.faq') }}" class="list-group-item small list-group-item-action d-flex align-items-center gap-2 link-dark">
					<i class="material-icons">quiz</i>
					FAQ Management
				</a>
				<a href="{{ route('root.elasticsearch') }}" class="list-group-item small list-group-item-action d-flex align-items-center gap-2 link-dark">
					<i class="material-icons">storage</i>
					Elasticsearch Monitor
				</a>
				<div class="card-body">
					<small class="card-title text-muted text-uppercase mb-0">Bot Menu</small>
				</div>
				@foreach (config('sources') as $key => $source)
					<a href="{{ route(implode('.', [ 'crawlers', $key ])) }}" class="list-group-item small list-group-item-action d-flex align-items-center gap-2 link-dark">
						<i class="material-icons">android</i>
						{{ $source['name'] }} Settings
					</a>
				@endforeach
			</div>
		@endif
		<div class="card-body">
			<small class="text-muted">© {{ date('Y') }} {{ config('app.name') }}<br />All rights reserved.</small><br />
			<a href="{{ route('faq.index') }}" class="link-dark">F.A.Q.</a>
		</div>
	</div>
</aside>
