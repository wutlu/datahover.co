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
			<p>DATAHOVER is a start-up project supported by Etsetra Analytics. Etsetra Analytics is a proven social media monitoring and analytics company in Turkey.</p>

			<p>As the DATAHOVER team, we have been working on social media data for more than 6 years. We know that everyone draws different conclusions from the same data. However, accessing data is as difficult a process as searching for gold. The analysis part is the easiest part of the job. We solve the hard part for you. We find data. We leave the analytics to you.</p>

			<p>Today, we are working with our London-based company to provide the best service to our valued users. As our users increase, the quality of our service will increase. Thank you for working with us.</p>
		</div>
	</div>

	@include('includes.footer')
@endsection
