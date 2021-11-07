<nav class="master shadow-sm bg-dark position-sticky top-0 right-0 left-0">
	<div class="container">
		<div class="d-flex justify-content-between align-items-center h-50px">
			<a href="{{ route('dashboard') }}">
				<img alt="Logo" src="{{ asset('images/icon.svg') }}" width="32" height="32" />
			</a>

			<div class="d-flex align-items-center gap-4">
				<div class="d-inline-block d-md-none">
					<a href="#" class="link-light drawer" data-class="body" data-class-toggle="drawer">
						<i class="material-icons--before"></i>
					</a>
				</div>
				<div class="dropdown">
					<a href="{{ route('dashboard') }}" class="dropdown-toggle link-light d-flex align-items-center gap-1" data-bs-toggle="dropdown" aria-expanded="false">
						@auth
							<img alt="Avatar" src="{{ auth()->user()->avatar }}" class="w-24px h-24px rounded-circle" />
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
			</div>
		</div>
	</div>
</nav>
