<div class="dropdown ms-auto">
	<a href="{{ route('dashboard') }}" class="dropdown-toggle link-light d-flex align-items-center gap-1" data-bs-toggle="dropdown" aria-expanded="false">
		@auth
			<small class="pe-1">{{ auth()->user()->name }}</small>
			<img alt="Avatar" src="{{ auth()->user()->avatar }}" class="w-32px h-32px rounded-circle" />
		@else
			<i class="material-icons">account_circle</i>
		@endauth
	</a>
	<ul class="dropdown-menu shadow rounded-0 dropdown-menu-dark dropdown-menu-end border-0">
		@auth
			<li>
				<span class="dropdown-header">{{ auth()->user()->email }}</span>
			</li>
			<li>
				<a class="dropdown-item d-flex align-items-center gap-3" href="{{ route('dashboard') }}">
					<i class="material-icons icon-sm">dashboard</i>
					Dashboard
				</a>
			</li>
			<li>
				<a class="dropdown-item d-flex align-items-center gap-3" href="{{ route('user.account') }}">
					<i class="material-icons icon-sm">account_circle</i>
					Account
				</a>
			</li>
			<li>
				<a class="dropdown-item d-flex align-items-center gap-3" href="#">
					<i class="material-icons icon-sm">receipt</i>
					Invoices
				</a>
			</li>
			<li class="dropdown-divider"></li>
			<li>
				<a class="dropdown-item d-flex align-items-center gap-3" href="{{ route('user.gate.exit') }}">
					<i class="material-icons icon-sm">logout</i>
					Logout
				</a>
			</li>
		@else
			<li>
				<a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('user.gate') }}">
					<i class="material-icons">home</i>
					Login
				</a>
			</li>
		@endauth
	</ul>
</div>
