@extends(
	'layouts.master',
	[
		'title' => 'About Us'
	]
)

@section('content')
	@component('includes.header')
		@slot('title', 'About Us')
		<br />
		<br />
		<br />
	@endcomponent

	<div class="container">
		<div class="mw-1024px mx-auto my-5 py-5">

		</div>
	</div>

	@include('includes.footer')
@endsection
