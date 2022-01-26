@extends(
	'layouts.master',
	[
		'title' => 'Dashboard',
		'master' => true,
		'breadcrumb' => [
			'Dashboard' => route('dashboard'),
			'Search Api' => '#',
		]
	]
)

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

    let __save = function(__, obj)
    {
        let save_modal = $('#save-modal');
            save_modal.find('form').find('input[name=name]').val('')
            save_modal.modal('hide')

        let created_modal = $('#created-modal');
            created_modal.find('input[name=xml_url]').val(obj.data.xml)
            created_modal.find('input[name=json_url]').val(obj.data.json)
            created_modal.modal('show')
    }
@endpush

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

    @component('includes.modals.modal', [
        'name' => 'save',
        'title' => 'Save as Feed'
    ])
        <form
            id="saveForm"
            autocomplete="off"
            method="post"
            action="#"
            data-blockui="#save-modal->find(.modal-content)"
            data-include="search"
            data-callback="__save"
            data-action="{{ route('feed.create') }}">

            <div class="form-floating mb-2">
                <input type="text" class="form-control shadow-sm rounded-0" name="name" id="name" />
                <label for="name">{{ __('validation.attributes.name') }}</label>
            </div>

            <div class="alert alert-info rounded-0 shadow-sm mb-4 d-flex align-items-center gap-3">
                <i class="material-icons">info</i>
                <small>Feed creation for those who don't want to use API. After you create it, you can edit it from the <strong>Feeds</strong> menu.</small>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-outline-secondary shadow-sm rounded-0">Save</button>
            </div>
        </form>
    @endcomponent

    @component('includes.modals.modal', [
        'name' => 'created',
        'title' => 'Feed created'
    ])
        <div class="form-floating mb-4">
            <input readonly data-copy="json_url" data-copied="Feed JSON url Copied!" class="form-control shadow-sm rounded-0" type="text" name="json_url" id="json_url" />
            <label for="json_url">{{ __('validation.attributes.json_url') }}</label>
        </div>
        <div class="form-floating mb-4">
            <input readonly data-copy="xml_url" data-copied="Feed XML url Copied!" class="form-control shadow-sm rounded-0" type="text" name="xml_url" id="xml_url" />
            <label for="xml_url">{{ __('validation.attributes.xml_url') }}</label>
        </div>

        <div class="d-flex align-items-center justify-content-end gap-3">
            <a href="{{ route('feed.index') }}" class="d-flex align-items-center gap-2 link-warning">
                <i class="material-icons">rss_feed</i>
                Go Feeds
            </a>
            <small class="text-muted">or</small>
            <button type="submit" class="btn btn-outline-secondary shadow-sm rounded-0" data-bs-dismiss="modal">Close</button>
        </div>
    @endcomponent
@endpush

@section('content')
	<div class="card rounded-0 shadow-sm" id="masterCard">
        <div class="card-body bg-light border-bottom">
            <div class="d-flex gap-2">
                <span class="card-title text-uppercase h6 fw-bold mb-0">Search Api</span>
                <small class="text-muted">
                    <span data-name="total">0</span> results found!
                </small>
            </div>
        </div>
        <div class="card-body">
            <small class="text-muted">Search</small>
            <div class="input-group input-group-lg d-flex flex-nowrap">
                <input
                    type="text"
                    class="form-control bg-light border shadow-sm rounded-0"
                    name="search"
                    id="search"
                    data-blockui="#masterCard"
                    data-reset="true"
                    data-action="true"
                    data-action-target="#items" />
                <button data-bs-toggle="modal" data-bs-target="#searchQueries-modal" class="btn btn-light border border-1 rounded-0 shadow-sm" type="button">
                    <i class="material-icons text-muted">info</i>
                </button>
            </div>
            <div class="d-flex justify-content-between py-1 mb-2">
                <small class="text-muted">Results of last 24 hours</small>
                <a href="#" class="small" data-bs-toggle="modal" data-bs-target="#save-modal">
                    <span data-bs-toggle="tooltip" data-bs-placement="left" title="You can convert your search to Feed here">Save as Feed</span>
                </a>
            </div>
        </div>
        <div
            id="items"
            class="list-group list-group-flush border-0"
            data-action="{{ route('api.search') }}"
            data-callback="__results"
            data-skip="0"
            data-take="10"
            data-include="search"
            data-headers='{"X-Api-Key": "{{ auth()->user()->api_key }}", "X-Api-Secret": "{{ auth()->user()->api_secret }}"}'
            data-more="#itemsMore"
            data-each="#items">
            <pre class="list-group-item list-group-item-action border-0 each-model mb-0"></pre>
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
        <div class="accordion" id="apiAccordion">
            @foreach ($apis as $key => $api)
                <div class="accordion-item border-0 rounded-0 border-top">
                    <div class="accordion-header">
                        <button
                            class="accordion-button bg-light rounded-0 shadow-none collapsed py-2"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $key }}">
                            <small class="text-muted">{{ $api['name'] }}</small>
                        </button>
                    </div>
                    <div id="{{ $key }}" class="accordion-collapse collapse border-0" data-bs-parent="#apiAccordion">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item ps-4 d-flex flex-column border-0">
                                <small class="text-muted">{{ $api['method'] }}</small>
                                <label>{{ $api['route'] }}</label>
                            </li>
                            <li class="list-group-item ps-4 d-flex flex-column border-0">
                                <small class="text-muted">Request Limit</small>
                                <label>{{ $rate_limit }} request per {{ $rate_minutes }} minutes</label>
                            </li>
                            <li class="list-group-item fw-bold border-0 text-uppercase">Headers</li>
                            <li class="list-group-item ps-4 d-flex flex-column border-0 py-1">
                                <small class="text-muted">X-Api-Key</small>
                                <label>{{ auth()->user()->api_key }}</label>
                            </li>
                            <li class="list-group-item ps-4 d-flex flex-column border-0 py-1">
                                <small class="text-muted">X-Api-Secret</small>
                                <label>{{ auth()->user()->api_secret }}</label>
                            </li>
                            <li class="list-group-item ps-4 d-flex flex-column border-0 py-1">
                                <small class="text-muted">Accept</small>
                                <label>application/json</label>
                            </li>

                            <li class="list-group-item fw-bold border-0 text-uppercase">Params</li>

                            @foreach ($api['params'] as $key => $value)
                                <li class="list-group-item ps-4 d-flex flex-column border-0 py-1">
                                    <small class="text-muted">{{ $key }}</small>
                                    <label>{{ $value }}</label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
	</div>
@endsection
