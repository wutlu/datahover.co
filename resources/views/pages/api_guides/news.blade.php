@extends(
	'layouts.master',
	[
		'title' => 'News API Guide',
		'description' => 'You can follow news sites with the News API. You can call the results to your server via instant or 15-minute feeds. '.config('app.name').' provides a clean API query, a clean result.'
	]
)

@section('content')
	@component('includes.header')
		@slot('title', 'News API')
		<div class="mw-1024px mx-auto p-5">
			<p class="lead text-white text-center mb-0">You can follow news sites with the News API. You can call the results to your server via instant or 15-minute feeds. {{ config('app.name') }} provides a clean API query, a clean result.</p>
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

		<div class="row mx-auto mb-5 pb-5">
			<div class="col-12 col-md-4">
				<small class="text-dark text-uppercase fw-bold">Where can I use the News API?</small>
			</div>
			<div class="col-12 col-md-8">
				<p class="lead mb-3">Audience detection for brands, reputation tracking for companies and institutions, social media listening, etc. It is very useful for various environments.</p>
				<p class="small mb-0 text-muted"><span class="fw-bold">{{ config('app.name') }}</span>, is the data collection tool for data analytics companies that need open source News data.</p>
			</div>
		</div>

		<div class="row mx-auto my-5 py-5">
			<div class="col-12 col-md-8 offset-md-2">
				<small class="text-dark text-uppercase fw-bold text-center d-block mb-4">How can I track a keyword?</small>
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
						<div style="padding:55.94% 0 0 0;position:relative;"><iframe src="{{ config('services.vimeo.tutorials.news_domain_tracking') }}" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe></div><script src="https://player.vimeo.com/api/player.js"></script>
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
						<small class="text-dark text-uppercase fw-bold d-block mb-4">Live News Feed Examples</small>
						<small class="text-muted">Unlike the standard API, with an update interval of 15 minutes.</small>
					</span>
					<div class="d-flex align-items-center justify-content-center gap-4">
						<a title="JSON Example" target="_blank" href="{{ config('services.datahover.feeds.news.json') }}" class="d-flex flex-column align-items-center text-center link-dark">
							<img alt="Folder" src="{{ asset('images/folder.svg') }}" class="w-32px h-32px" />
							<small>biden.JSON</small>
						</a>
						<a title="XML Example" target="_blank" href="{{ config('services.datahover.feeds.news.xml') }}" class="d-flex flex-column align-items-center text-center link-dark">
							<img alt="Folder" src="{{ asset('images/folder.svg') }}" class="w-32px h-32px" />
							<small>biden.XML</small>
						</a>
					</div>
				</div>
			</div>
		</div>

		<div class="row mx-auto my-5 py-5">
			<div class="col-12 col-md-4">
				<small class="text-dark text-uppercase fw-bold d-block mb-4">Why {{ config('app.name') }} News API?</small>
			</div>
			<div class="col-12 col-md-8">
				<ol class="list-group list-group-numbered shadow-sm">
					<li class="list-group-item">No news markup! You just need to enter the domain name.</li>
					<li class="list-group-item">Don't get lost in complex API documentation. Everything is very easy. Just track the domain and query it.</li>
					<li class="list-group-item">Do a full-text search on articles.</li>
					<li class="list-group-item">You can filter by all data fields.</li>
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
  "id": "2e564ad9119b0e07df33c5b08aa8f7ea",
  "site": "nytimes.com",
  "link": "nytimes.com/2022/01/25/style/cannabis-events-new-york-city.html",
  "device": "Web",
  "status": "ok",
  "called_at": "2022-01-28T23:55:13+00:00",
  "image": "https://static01.nyt.com/images/2022/01/27/fashion/HAPPY-MUNKEY-1/HAPPY-MUNKEY-1-facebookJumbo.jpg",
  "created_at": "2022-01-25T21:02:23+00:00",
  "text": "Advertisement Supported by As New Yorkers wait for marijuana sales to be legalized, some small businesses are putting down roots. By  Pierre-Antoine Louis New York may have  legalized recreational marijuana  in 2021, but the state still has not passed the laws that would allow its sale in stores, or its consumption at club-like lounges — moves that would open doors for a potential  $4.2 billion industry . Though licenses and permits are not expected to be issued until later this year, with businesses up and running in 2023, the recreational marijuana industry is already setting down roots, hosting events aimed at educating consumers and drumming up excitement. In November, the Cannabis World Congress and Business Exposition descended upon the Javits Center. Over the course of three days, vendors showed off goods they hope to one day sell in New York, and attendees spoke about their eagerness to see the stigma of cannabis consumption erased. Arnaud Dumas de Rauly, a co-founder and the C.E.O. of  the Blinc Group , which makes and sells vapes, spoke about the purpose of the event. “I think it’s just creating a lot of buzz,” he said. “But still, we’re in this middle ground where everything’s open and people are going to come here to learn about the industry.” Once legalization goes into effect, he expects to see new kinds of spaces opening in the city that cater to cannabis users. “I would love to see consumption lounges pop up here,” Mr. Arnaud said. Faye Coleman, the C.E.O. of  Pure Genesis , a cannabis business with a social mission, attended the expo to showcase her company’s products and to talk about equity in the industry in terms of agency and access. Historically,  Black and Latino people have been arrested and jailed for marijuana-related crimes at far higher rates than white people ; for that reason, some cannabis activists say, many have been  reluctant to align themselves with cannabis-related businesses , even legal ones. “Those are some of the things that are plaguing diversity, equity and inclusion, and I would say stigma and confusion,” Ms. Coleman said. Hundreds of attendees at the expo participated in conversations about the future of legal cannabis, panels on cultivation techniques, marketing, sustainability, and presentations that provided insight into international markets. All around were indications of what New York City could look like as soon as all the regulations are enacted. Last summer, a New York City lifestyle brand called  Happy Munkey  tested the waters for “consumption events” with a couple of cannabis-infused nights at the  Immersive Van Gogh  exhibit at Pier 36, a 75,000-square-foot waterfront space on the Lower East Side of Manhattan. Masked-up guests, who were instructed to wear all-white outfits, walked into the lobby to view an imaginative 3-D recreation of Vincent Van Gogh’s “Starry Night” (which featured about 7,500 painted brushes by the designer David Korins), then made their way through a gallery and into the adroit work and vision of the painter. There was no marijuana consumption allowed inside the gallery. Outside, however, smoke filled the waterfront air and edibles were consumed. Technically, no marijuana was sold at the event. But included in the Happy Munkey package, along with the pass for Immersive Van Gogh, were generously packed pre-rolled joints. The event was advertised as “B.Y.O.C.” — “bring your own cannabis.” Happy Munkey’s co-founders, Ramon Reyes and Vladimir Bautista — who are both from the Dominican Republic and grew up in New York City — started the brand five years ago and began hosting cannabis-related events in 2017. After Mr. Reyes traveled to Amsterdam, where he visited several cannabis coffee shops, he told Mr. Bautista he wanted to bring a similar feel and experience to their own city, with a local twist. “New York is just going to give it that pizazz that we need,” he recalled telling his business partner. “That New York touch. That New York fly life.” Chantaé Vetrice , a hip-hop and pop artist, attended the Immersive Van Gogh premiere with her boyfriend, Stephen Ship. While there, she joked with the exhibition’s head of marketing, Keith Hurd, about wanting to view the exhibition under the influence of marijuana. The next day, Mr. Hurd called them and asked: How could they do this as an actual event? Mr. Ship, who is friends with Mr. Hurd, suggested Ms. Vetrice reach out to the Happy Munkey founders to see if they’d be interested in collaborating. The company — which, in addition to hosting events, sells merchandise and produces multimedia content — had already helped her produce a single, “Elevated.” “We set up the meetings with Keith, Vlad and Ramon,” Ms. Vetrice said. “They loved the idea and they just went for it. And it was a success.” She hopes that further collaborations between arts organizations and cannabis companies are on the way. “I think it can bring two separate communities together and kind of destigmatize the use of cannabis,” Ms. Vetrice said. Maria Shclover, a producer of the Immersive Van Gogh   exhibition, also expressed enthusiasm for the synergy. “Happy Munkey is a minority-owned local business, and we love to support minority owned businesses, because we are first-generation immigrants ourselves,” she said. “Now that cannabis is legal, this partnership seemed like a very good New York thing to do.” Beyond Happy Munkey, which has made its previously invitation-only events open to the public, there are other signs of the New York that’s to come. The Astor Club , a members-only cannabis club that opened on the Lower East Side in 2020, already draws a who’s who of the pot industry insiders. And large events appear to be here to stay: The next Cannabis World Congress and Business Expo will be this summer. And Happy Munkey’s next cannabis consumption event will be held, fittingly, on April 20. Advertisement",
  "title": "Cannabis Events Come Out of Hiding in New York City",
  "lang": "en"
}`))
	})
@endpush
