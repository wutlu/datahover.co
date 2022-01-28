<div class="dropdown ms-auto">
	<a title="Dashboard" href="{{ route('dashboard') }}" class="dropdown-toggle link-light d-flex align-items-center gap-1" data-bs-toggle="dropdown" aria-expanded="false">
		@auth
			<small class="pe-1">{{ auth()->user()->name }}</small>
			<img alt="Avatar" src="{{ auth()->user()->avatar }}" class="w-32px h-32px rounded-circle" />
		@else
			<i class="material-icons">account_circle</i>
		@endauth
	</a>
	<ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
		@auth
			<li>
				<span class="dropdown-header">{{ auth()->user()->email }}</span>
			</li>
			<li>
				<a title="Dashboard" class="dropdown-item d-flex align-items-center gap-3" href="{{ route('dashboard') }}">
					<i class="material-icons icon-sm">dashboard</i>
					Dashboard
				</a>
			</li>
			<li>
				<a title="Account" class="dropdown-item d-flex align-items-center gap-3" href="{{ route('user.account') }}">
					<i class="material-icons icon-sm">account_circle</i>
					Account
				</a>
			</li>
			<li>
				<a title="Invoices" class="dropdown-item d-flex align-items-center gap-3" href="{{ route('payments') }}">
					<i class="material-icons icon-sm">receipt</i>
					<div class="d-flex flex-column">
						<span>Payments</span>
						<small class="text-muted">Invoices</small>
					</div>
				</a>
			</li>
			<li>
				<a title="Frequently Asked Questions" class="dropdown-item d-flex align-items-center gap-3" href="{{ route('faq.index') }}">
					<i class="material-icons icon-sm">quiz</i>
					F.A.Q.
				</a>
			</li>
			<li>
				<a title="Logout" class="dropdown-item d-flex align-items-center gap-3" href="{{ route('user.gate.exit') }}">
					<i class="material-icons icon-sm">logout</i>
					Logout
				</a>
			</li>
		@else
			<li>
				<a title="Login" class="dropdown-item d-flex align-items-center gap-3" href="{{ route('user.gate') }}">
					<i class="material-icons icon-sm">home</i>
					Login
				</a>
			</li>
		@endauth
	</ul>
</div>
