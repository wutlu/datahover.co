@push('css')
	.footer-up {
		background-image: url('{{ asset('images/logo-half.svg') }}');
		background-repeat: no-repeat;
		background-position: center bottom;
		background-size: 100% auto;
		height: 14vw;
	}

	.social-icon {
		filter: grayscale(1);
  		transition: all 200ms cubic-bezier(.4, .0, .2, 1);
  		background-color: #fff;
	}
	.social-icon:hover {
		filter: grayscale(0);
	}
@endpush

<div class="footer-up"></div>

<footer class="bg-dark">
	<div class="container">
		<div class="py-5">
			<div class="row">
				<div class="col-6 col-md-4">
					<div class="d-flex flex-column mb-4">
						<img alt="Logo" src="{{ asset('images/logo-white.svg') }}" width="164" class="h-auto mb-2" />
						<small class="mb-4 text-muted mw-300px">{{ config('etsetra.info') }}</small>
						<p class="mb-0 text-muted d-flex flex-column">
							<span class="fw-bold">© {{ date('Y') }} {{ config('app.name') }}</span>
							<small>All rights reserved.</small>
						</p>
					</div>
				</div>
				<div class="col-6 col-md-4">
					<div class="d-flex flex-column mb-4">
						<small class="mb-2 text-muted mw-300px">{!! config('etsetra.address') !!}</small>
						<a title="Contact" href="mailto:{{ config('etsetra.email') }}" class="text-muted mb-4">{{ config('etsetra.email') }}</a>

						<div class="d-flex align-items-center gap-1">
							@foreach (config('etsetra.social') as $key => $social)
								@if ($social['profile_url'])
									<a class="social-icon rounded shadow-sm p-1" target="_blank" title="{{ $social['name'] }}" rel="dofollow" href="{{ $social['profile_url'] }}">
										<img alt="{{ $social['name'] }}" src="{{ asset('images/icons/'.$key.'.png') }}" class="w-24px h-24px" />
									</a>
								@endif
							@endforeach
						</div>
					</div>
				</div>
				<div class="col-6 col-md-2">
					<div class="d-flex flex-column mb-4">
						<small class="mb-1 text-muted text-uppercase">Info</small>
						<a title="About Us" href="{{ route('page', [ 'base' => 'page', 'name' => 'about-us' ]) }}" class="link-light">About Us</a>
						<a title="Privacy Policy" href="{{ route('page', [ 'base' => 'legal', 'name' => 'privacy-policy' ]) }}" class="link-light">Privacy Policy</a>
						<a title="Terms of Service" href="{{ route('page', [ 'base' => 'legal', 'name' => 'terms-of-service' ]) }}" class="link-light">Terms of Service</a>
						<a title="Frequently Asked Questions" href="{{ route('faq.index') }}" class="link-light">F.A.Q.</a>
					</div>
				</div>
				<div class="col-6 col-md-2">
					<div class="d-flex flex-column mb-4">
						<small class="mb-1 text-muted text-uppercase">API Guides</small>
						@foreach (config('sources') as $key => $item)
							<a title="{{ $item['name'] }}" href="{{ route('page', [ 'base' => 'api-guide', 'name' => $key ]) }}" class="link-light">{{ $item['name'] }} API</a>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
</footer>
