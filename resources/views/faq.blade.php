@extends(
	'layouts.master',
	[
		'title' => 'Frequently Asked Questions'
	]
)

@push('js')
    let __items = function(__, o)
    {
    	__.find('.accordion-button').attr('data-bs-target', '#collapse-' + o.id).html(o.question)
    	__.find('.accordion-collapse').attr('id', 'collapse-' + o.id).find('pre').html(o.answer)
    }
@endpush

@section('content')
	@component('includes.header')
		@slot('title', 'F.A.Q.')
		    <input
		        type="text"
		        class="form-control shadow-sm border-0 rounded-0 mw-300px mx-auto"
		        name="search"
		        data-blockui="#items"
		        data-reset="true"
		        data-action="true"
		        data-action-target="#items"
		        placeholder="Search" />
		<br />
		<br />
		<br />
	@endcomponent

	<div class="container">
		<div class="mw-1024px mx-auto my-5 py-5">
			<div
				id="items"
				class="accordion accordion-flush load shadow-sm"
				data-action="{{ route('faq.list') }}"
				data-include="search"
				data-each="#items">

				<div class="accordion-item each-model mb-2">
					<h2 class="accordion-header">
						<a href="#" class="accordion-button collapsed shadow-sm border-bottom fw-bold" data-bs-toggle="collapse"></a>
					</h2>
					<div class="accordion-collapse collapse" data-bs-parent="#items">
						<div class="accordion-body">
							<pre class="mb-0"></pre>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	@include('includes.footer')
@endsection
