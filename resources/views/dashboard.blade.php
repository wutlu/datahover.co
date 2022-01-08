@extends(
	'layouts.master',
	[
		'title' => 'Dashboard',
		'master' => true,
		'breadcrumb' => [
			'Dashboard' => route('dashboard')
		]
	]
)

@push('footer')
	@include('includes.modals.help', [
		'name' => 'searchQueries',
		'title' => 'Search Tips',
		'lines' => [
			'To do a casual search, just type the word you want to search for.',
			'Use <b>"double quotes"</b> to search for sentences.',
			'You can use <b>OR</b> and <b>AND</b> between words. Or you can group words with <b>(parentheses)</b>.',
			'You can add <b>-hyphens</b> for words, groups or sentences you don\'t want.',
			'By typing <b>site:twitter.com</b> or <b>site:bbc.com</b>, you can only see the content of those sites. For the site you don\'t want, use <b>-site:hyphens.com</b>.',
			'You can filter with parameters such as <b>title:Hello</b>, <b>device:Web</b> and <b>lang:en</b>.',
		]
	])
@endpush

@section('content')
{{-- 	<div class="card bg-transparent border-0 rounded-0 mb-4">
		<div id="greetingCarousel" class="carousel carousel-dark" data-bs-ride="carousel" data-bs-wrap="false" data-bs-interval="false">
			<div class="carousel-indicators">
				<button class="active" type="button" data-bs-target="#greetingCarousel" data-bs-slide-to="0"></button>
				<button type="button" data-bs-target="#greetingCarousel" data-bs-slide-to="1"></button>
				<button type="button" data-bs-target="#greetingCarousel" data-bs-slide-to="2"></button>
			</div>
			<div class="carousel-inner">
				<div class="carousel-item h-200px active">
					<div class="carousel-caption d-flex flex-column justify-content-center top-0 bottom-0">
						<h5 class="fw-bold text-uppercase">Specify Track</h5>
						<p class="text-muted">We will scan the internet for you. However, you need to specify criteria for us.</p>
					</div>
				</div>
				<div class="carousel-item h-200px">
					<div class="carousel-caption d-flex flex-column justify-content-center top-0 bottom-0">
						<h5 class="fw-bold text-uppercase">Connect to Our API's</h5>
						<p class="text-muted">Use our Search API. Get all the content on your server according to the criteria you want.</p>
					</div>
				</div>
				<div class="carousel-item h-200px">
					<div class="carousel-caption d-flex flex-column justify-content-center top-0 bottom-0">
						<h5 class="fw-bold text-uppercase">Automation</h5>
						<p class="text-muted">Automate your own with our track API's!</p>
					</div>
				</div>
			</div>
			<button class="carousel-control-prev" type="button" data-bs-target="#greetingCarousel" data-bs-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Previous</span>
			</button>
			<button class="carousel-control-next" type="button" data-bs-target="#greetingCarousel" data-bs-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Next</span>
			</button>
		</div>
	</div> --}}

	<div class="row">
		<div class="col-6 col-xl-3">
			<div class="card card-coverage border-0 rounded-0 mb-5 bg-transparent">
				<div class="card-body h-200px d-flex flex-column justify-content-between bg-transparent">
					<span class="h4 fw-bold mb-0">Best social data technology</span>
					<small class="h6 fw-bold mb-0">
						<img alt="Logo" src="{{ asset('images/logo.svg') }}" class="img-fluid mw-64px" />
					</small>
				</div>
			</div>
		</div>
		<div class="col-6 col-xl-3">
			<div class="card card-coverage border-0 rounded-0 shadow-sm mb-5" style="background-image: url('{{ asset('images/news.jpg') }}');">
				<div class="card-body h-200px d-flex flex-column justify-content-between">
					<small class="h6 fw-bold text-white mb-0">NEWS</small>
					<span class="h4 fw-bold text-white mb-0">Track news sites</span>
				</div>
			</div>
		</div>
		<div class="col-6 col-xl-3">
			<div class="card card-coverage border-0 rounded-0 shadow-sm mb-5" style="background-image: url('{{ asset('images/social.jpg') }}');">
				<div class="card-body h-200px d-flex flex-column justify-content-between">
					<small class="h6 fw-bold text-white mb-0">SOCIAL</small>
					<span class="h4 fw-bold text-white mb-0">We are working on more resources</span>
				</div>
			</div>
		</div>
		<div class="col-6 col-xl-3">
			<div class="card card-coverage border-0 rounded-0 shadow-sm mb-5" style="background-image: url('{{ asset('images/stats.jpg') }}');">
				<div class="card-body h-200px d-flex flex-column justify-content-between">
					<small class="h6 fw-bold text-white mb-0">STATISTICS</small>
					<span class="h4 fw-bold text-white mb-0">Big data for detailed statistics</span>
				</div>
			</div>
		</div>
	</div>

	<div class="card rounded-0 shadow-sm" id="masterCard">
        <div class="card-body">
            <div class="d-flex gap-2">
                <span class="card-title text-uppercase h6 fw-bold mb-0">Try Search Api</span>
                <small class="text-muted">
                    <span data-name="total">0</span> results found!
                </small>
            </div>
        </div>
        @component('includes.api')
        	@slot('apis', $apis)
        	@slot('rate_limit', $rate_limit)
        	@slot('rate_minutes', $rate_minutes)
        @endcomponent
        <div class="input-group input-group-lg d-flex flex-nowrap">
            <input
                type="text"
                class="form-control border shadow-sm border-start-0 rounded-0 text-success bg-success bg-opacity-10"
                placeholder="Write something"
                name="search"
                id="search"
                value="site:foxnews.com"
                data-blockui="#masterCard"
                data-reset="true"
                data-action="true"
                data-action-target="#items" />
            <button data-bs-toggle="modal" data-bs-target="#searchQueries-modal" class="btn btn-light border border-1 border-end-0 rounded-0 shadow-sm" type="button">
                <i class="material-icons text-muted">info</i>
            </button>
        </div>
        <div
            id="items"
            class="list-group list-group-flush border-0 load"
            data-action="{{ route('api.search') }}"
            data-callback="__results"
            data-skip="0"
            data-take="10"
            data-include="search"
            data-headers='{"X-Api-Key": "{{ auth()->user()->api_key }}", "X-Api-Secret": "{{ auth()->user()->api_secret }}"}'
            data-more="#itemsMore"
            data-each="#items">
            <pre class="list-group-item border-0 each-model"></pre>
        </div>
        <a
            href="#"
            id="itemsMore"
            class="d-none py-1"
            data-blockui="#masterCard"
            data-action="true"
            data-action-target="#items">
            <i class="material-icons d-table mx-auto text-muted">more_horiz</i>
        </a>
	</div>
@endsection

@push('js')
	function __results(__, obj)
	{
		$('[data-name=total]').html(obj.stats.total)
	}

	function __items(__, o)
	{
		__.html(JSON.stringify(o, null, 2))
	}

    const driver = new app.Driver();

    app.info('search.api', function() {
        driver.highlight({
            element: '.accordion-header',
            popover: {
                title: 'Api documents are below the cards',
                position: 'top',
                showButtons: false,
            }
        })
    }, true)

    $(document).on('show.bs.collapse','#apiAccordion', function () {
        driver.reset()
    })
@endpush
