@extends(
	'layouts.master',
	[
		'title' => 'Payment Status',
		'master' => true,
		'breadcrumb' => [
			'Dashboard' => route('dashboard'),
			'Subscription' => route('subscription.index'),
			'Payment Status' => '#',
		]
	]
)

@push('css')
	.path {
	  stroke-dasharray: 1000;
	  stroke-dashoffset: 0;
	}
	.path.circle {
	  -webkit-animation: dash 0.9s ease-in-out;
	  animation: dash 0.9s ease-in-out;
	}
	.path.line {
	  stroke-dashoffset: 1000;
	  -webkit-animation: dash 0.9s 0.35s ease-in-out forwards;
	  animation: dash 0.9s 0.35s ease-in-out forwards;
	}
	.path.check {
	  stroke-dashoffset: -100;
	  -webkit-animation: dash-check 0.9s 0.35s ease-in-out forwards;
	  animation: dash-check 0.9s 0.35s ease-in-out forwards;
	}

	p {
	  font-size: 1.25em;
	}
	p.success { color: #73af55; }
	p.error { color: #d06079; }

	@-webkit-keyframes dash { 0% { stroke-dashoffset: 1000; } 100% { stroke-dashoffset: 0; } }
	@keyframes dash { 0% { stroke-dashoffset: 1000; } 100% { stroke-dashoffset: 0; } }
	@-webkit-keyframes dash-check { 0% { stroke-dashoffset: -100; } 100% { stroke-dashoffset: 900; } }
	@keyframes dash-check { 0% { stroke-dashoffset: -100; } 100% { stroke-dashoffset: 900; } }
@endpush

@section('content')
	<div class="my-5">
		@switch($status)
			@case('success')
				<div class="d-flex flex-column align-items-center gap-4">
					<svg class="w-128px" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
						<circle class="path circle" fill="none" stroke="#73AF55" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
						<polyline class="path check" fill="none" stroke="#73AF55" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/>
					</svg>
					<p class="success mb-5 text-center mw-300px">Your account has been charged. After <span class="intdown fw-bold">5</span> seconds you will be redirected to the <a href="{{ route('subscription.index') }}" class="text-underline link-success fw-bold">Subscription Management</a> page.</p>
				</div>
			@break
			@case('cancel')
				<div class="d-flex flex-column align-items-center gap-4">
					<svg class="w-128px" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
						<circle class="path circle" fill="none" stroke="#D06079" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
						<line class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3"/>
						<line class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2"/>
					</svg>
					<p class="error mb-5 text-center mw-300px">Payment failed! After <span class="intdown fw-bold">5</span> seconds you will be redirected to the <a href="{{ route('subscription.index') }}" class="text-underline link-danger fw-bold">Subscription Management</a> page.</p>
				</div>
			@break
		@endswitch
	</div>
@endsection

@push('js')
	$(window).on('load', function() {
		window.setTimeout(function() {
			window.location.href = '{{ route('subscription.index') }}';
		}, 5000)

		window.setInterval(function() {
			let el = $('.intdown');
				el.html(el.text() - 1)
		}, 1000)
	})
@endpush
