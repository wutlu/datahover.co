@push('css')
	.footer-up {
		background-image: url('{{ asset('images/logo-half.svg') }}');
		background-repeat: no-repeat;
		background-position: center bottom;
		background-size: 100% auto;
		height: 14vw;
	}
@endpush

<div class="footer-up"></div>
<footer class="bg-dark">
	<div class="container">
		<div class="py-5">
			<div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-between gap-4">
				<div class="d-flex flex-column text-center text-lg-start">
					<img alt="Logo" src="{{ asset('images/logo-white.svg') }}" width="164" height="auto" class="mx-auto mx-lg-0 mb-2" />
					<small class="mb-4 text-muted mw-300px">{{ config('etsetra.info') }}</small>
					<p class="mb-0 text-muted">© {{ date('Y') }} {{ config('app.name') }}</p>
				</div>
				<div class="d-flex flex-column align-items-center align-items-lg-start">
					<a href="{{ route('page', 'about-us') }}" class="link-light">About Us</a>
					<a href="{{ route('page', 'public-offer-agreement') }}" class="link-light">Public Offer Agreement</a>
					<a href="{{ route('page', 'privacy-policy') }}" class="link-light">Privacy Policy</a>
				</div>
				<div class="d-flex flex-column text-center text-lg-end">
					<small class="mb-2 text-muted mw-300px">{{ config('etsetra.address') }}</small>
					<a href="mailto:{{ config('etsetra.email') }}" class="text-muted">{{ config('etsetra.email') }}</a>
				</div>
			</div>
		</div>
	</div>
</footer>
