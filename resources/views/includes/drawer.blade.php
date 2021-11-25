<aside class="drawer">
	<div class="card shadow-sm position-initial border-sm-1 rounded-0 shadow-sm">
		<div class="card-body d-flex flex-column gap-3">
			<div class="d-flex flex-column flex-xl-row justify-content-between align-xl-items-center">
				<h6 class="card-title fw-bold mb-0">{{ auth()->user()->subscription()->plan['name'] }}</h6>
				<small class="text-muted">{{ auth()->user()->subscription()->days }} days left</small>
			</div>

			@if (auth()->user()->subscription == 'trial')
				<small class="text-muted rounded-0 mb-0 d-flex align-items-center gap-3">
					<i class="material-icons">warning</i>
					You are using a trial subscription. For a better service, please choose a package.
				</small>
			@else
				@if (auth()->user()->subscription()->days == 0)
					<p class="card-text text-danger h6 mb-0">Your subscription has expired!</p>
				@elseif (auth()->user()->subscription()->days <= 7)
					<p class="card-text text-muted h6 mb-0">Expires on {{ date('M d\t\h Y', strtotime(auth()->user()->subscription_end_date)) }}</p>
				@endif
			@endif

			<a href="{{ route('subscription.index') }}" class="btn btn-sm btn-primary d-block rounded-0 shadow-sm">Manage Subscription</a>
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
	</div>
</aside>
