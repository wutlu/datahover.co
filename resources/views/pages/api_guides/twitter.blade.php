@extends(
	'layouts.master',
	[
		'title' => 'Twitter API Guide',
		'description' => 'You can follow Twitter keywords with the Instagram API. You can call the results to your server via instant or 15-minute feeds. '.config('app.name').' provides a clean API query, a clean result.'
	]
)

@section('content')
	@component('includes.header')
		@slot('title', 'Twitter API')
		<div class="mw-1024px mx-auto p-5">
			<p class="lead text-white text-center mb-0">You can follow Twitter keywords with the Twitter API. You can call the results to your server via instant or 15-minute feeds. {{ config('app.name') }} provides a clean API query, a clean result.</p>
		</div>
		<br />
		<br />
		<br />
		<br />
	@endcomponent

	<div class="container">
		<div class="row mx-auto position-relative" style="top: -6rem;">
			<div class="col-12 col-md-8 offset-md-2">
				<pre class="json-hl bg-grey rounded shadow-sm p-2"></pre>
			</div>
		</div>

		<div class="row mx-auto my-5 py-5">
			<div class="col-12 col-md-4">
				<small class="text-dark text-uppercase fw-bold">Where can I use the Twitter API?</small>
			</div>
			<div class="col-12 col-md-8">
				<p class="lead mb-3">Audience detection for brands, reputation tracking for companies and institutions, social media listening, etc. It is very useful for various environments.</p>
				<p class="small mb-0 text-muted"><span class="fw-bold">{{ config('app.name') }}</span>, is the data collection tool for data analytics companies that need open source Twitter data.</p>
			</div>
		</div>

		<div class="row mx-auto my-5 py-5">
			<div class="col-12 col-md-8 offset-md-2">
				<small class="text-dark text-uppercase fw-bold text-center d-block mb-5">How can I track a keyword?</small>
				<ul class="nav nav-tabs flex-nowrap" role="tablist">
					<li class="nav-item" role="presentation">
						<a href="#" class="small nav-link active" data-bs-toggle="tab" data-bs-target="#tracks" role="tab">Tracks</a>
					</li>
					<li class="nav-item" role="presentation">
						<a href="#" class="small nav-link" data-bs-toggle="tab" data-bs-target="#search-feeds" role="tab">Search & Feeds</a>
					</li>
					<li class="nav-item" role="presentation">
						<a href="#" class="small nav-link" data-bs-toggle="tab" data-bs-target="#api" role="tab">API</a>
					</li>
				</ul>
				<div class="tab-content border border-top-0 rounded-0 rounded-bottom shadow-sm">
					<div class="tab-pane bg-white p-3 show active" id="tracks" role="tabpanel">
						<div style="padding:55.94% 0 0 0;position:relative;"><iframe src="{{ config('services.vimeo.tutorials.twitter_keyword_tracking') }}" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe></div><script src="https://player.vimeo.com/api/player.js"></script>
					</div>
					<div class="tab-pane bg-white p-3" id="search-feeds" role="tabpanel">
						<div style="padding:55.94% 0 0 0;position:relative;"><iframe src="{{ config('services.vimeo.tutorials.search_api') }}" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe></div><script src="https://player.vimeo.com/api/player.js"></script>
					</div>
					<div class="tab-pane bg-white p-3" id="api" role="tabpanel">
						<div class="border border-top-0">
					        @component('includes.api')
					            @slot('apis', $apis)
					            @slot('rate_limit', $rate_limit)
					            @slot('rate_minutes', $rate_minutes)
					        @endcomponent
						</div>
					</div>
				</div>

				<div class="my-5 py-5 d-flex flex-column flex-lg-row gap-5 p-5 align-items-center justify-content-between bg-grey rounded shadow-sm">
					<span class="d-flex flex-column">
						<small class="text-dark text-uppercase fw-bold">Live Twitter Feed Examples</small>
						<small class="text-muted">Unlike the standard API, with an update interval of 15 minutes.</small>
					</span>
					<div class="d-flex align-items-center justify-content-center gap-4">
						<a title="JSON Example" target="_blank" href="{{ config('services.datahover.feeds.twitter.json') }}" class="d-flex flex-column align-items-center text-center link-dark">
							<img alt="Folder" src="{{ asset('images/folder.svg') }}" class="w-32px h-32px" />
							<small>biden.JSON</small>
						</a>
						<a title="XML Example" target="_blank" href="{{ config('services.datahover.feeds.twitter.xml') }}" class="d-flex flex-column align-items-center text-center link-dark">
							<img alt="Folder" src="{{ asset('images/folder.svg') }}" class="w-32px h-32px" />
							<small>biden.XML</small>
						</a>
					</div>
				</div>
			</div>
		</div>

		<div class="row mx-auto my-5 py-5">
			<div class="col-12 col-md-4">
				<small class="text-dark text-uppercase fw-bold">Why {{ config('app.name') }} Twitter API?</small>
			</div>
			<div class="col-12 col-md-8">
				<ol class="list-group list-group-numbered shadow-sm">
					<li class="list-group-item">Don't waste your time with the necessary applications for the developer account.</li>
					<li class="list-group-item">Don't get hung up on the limits. Just focus on the data.</li>
					<li class="list-group-item">Don't get lost in complex API documentation. Everything is very easy. Just track the keyword and query it.</li>
					<li class="list-group-item">Do a full-text search on posts and comments.</li>
					<li class="list-group-item">You can filter by all data fields.</li>
					<li class="list-group-item">Don't pay third parties high prices for Twitter data.</li>
					<li class="list-group-item">Reach more data with unlimited access to the common data repository.</li>
				</ol>
			</div>
		</div>

		<div class="d-flex flex-column justify-content-center align-items-center gap-3 my-5 py-5 text-center">
			<p class="lead mb-0">“Talk is cheap. Show me the code.”</p>
			<img alt="Linus Torvalds" src="{{ asset('images/linus-torvalds.jpg') }}" class="w-64px h-64px rounded-circle shadow-sm" />
			<small class="text-muted">Linus Torvalds</small>
		</div>
		<div class="d-flex justify-content-center my-5 py-5">
			<a title="Try it" href="{{ route(auth()->check() ? 'dashboard' : 'user.gate') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4 shadow-sm">Try it</a>
		</div>
	</div>
	@include('includes.footer')
@endsection

@push('js')
	$(document).ready(function() {
		$('pre').html(app.jsonHL(`{
  "status": "ok",
  "id": "9e6f9c843e902dc9f2dc366da76b3866",
  "site": "twitter.com",
  "link": "https://twitter.com/slaysbellarke/status/1487173360501116936",
  "text": "AIUTO",
  "lang": "it",
  "device": "Twitter for iPhone",
  "user": {
    "id": "348972423",
    "name": "slaysbellarke",
    "title": "ac",
    "image": "http://pbs.twimg.com/profile_images/1452210472019759105/cdFe9EVC.jpg",
    "description": "solitamente dormo, mangio e ho l’ansia"
  },
  "created_at": "2022-01-28T21:18:54+00:00",
  "called_at": "2022-01-28T21:18:59+00:00"
}`))
	})
@endpush
