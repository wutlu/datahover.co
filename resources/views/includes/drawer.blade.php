<aside class="drawer">
	<div class="card shadow-sm position-initial border-sm-1 rounded-0 shadow-sm">
		<div class="card-body">
			<small class="card-title text-muted text-uppercase mb-0">Subscription</small>
		</div>
		<div class="card-body py-0">
			@if (auth()->user()->subscription()->package == 'demo')
				<a href="#" class="btn btn-outline-primary d-flex align-items-center gap-2 rounded-0">
					<i class="material-icons">upgrade</i>
					Upgrade
				</a>
			@else
				<span class="card-title">{{ auth()->user()->subscription()->days }} days left</span>
				@if (auth()->user()->subscription()->days == 0)
					<p class="card-text"><span class="bg-danger">Your subscription has expired!</span></p>
				@elseif (auth()->user()->subscription()->days <= 7)
					<p class="card-text"><span class="bg-warning">Your subscription period is about to expire.</span></p>
				@endif
			@endif
		</div>
		<div class="card-body">
			<small class="card-title text-muted text-uppercase mb-0">Menu</small>
		</div>
		<div class="list-group list-group-flush rounded-0">
			<a href="{{ route('track.dashboard') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 link-dark">
				<i class="material-icons">scatter_plot</i>
				Track List
			</a>
			<a href="#" class="list-group-item list-group-item-action d-flex align-items-center gap-2 link-dark">
				<i class="material-icons">search</i>
				Test Search
			</a>
		</div>
		@if (auth()->user()->is_root)
			<div class="card-body">
				<small class="card-title text-muted text-uppercase mb-0">ROOT Menu</small>
			</div>
			<div class="list-group list-group-flush rounded-0">
				<a href="{{ route('root.users') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 link-dark">
					<i class="material-icons">people</i>
					Users
				</a>
				<a href="{{ route('root.tracks') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 link-dark">
					<i class="material-icons">scatter_plot</i>
					All Tracks
				</a>
				<a href="{{ route('root.proxies') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 link-dark">
					<i class="material-icons">filter_4</i>
					Proxy Settings
				</a>
				<a href="{{ route('root.elasticsearch') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 link-dark">
					<i class="material-icons">storage</i>
					Elasticsearch Monitor
				</a>
				<div class="card-body">
					<small class="card-title text-muted text-uppercase mb-0">Bot Menu</small>
				</div>
				@foreach (config('sources') as $key => $source)
					<a href="{{ route(implode('.', [ 'crawlers', $key ])) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 link-dark">
						<i class="material-icons">android</i>
						{{ $source['name'] }} Settings
					</a>
				@endforeach
			</div>
		@endif
	</div>
</aside>
