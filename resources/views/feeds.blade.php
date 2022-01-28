@extends(
    'layouts.master',
    [
        'title' => 'Feeds',
        'master' => true,
        'breadcrumb' => [
            'Dashboard' => route('dashboard'),
            'Feeds' => '#'
        ]
    ]
)

@push('js')
    let __items = function(__, o)
    {
        __.find('[data-name=json]').attr('href', '{{ url('/') }}/storage/feeds/' + o.key + '/file.json')
        __.find('[data-name=xml]').attr('href', '{{ url('/') }}/storage/feeds/' + o.key + '/file.xml')
    }

    let __results = function(__, obj)
    {
        $('[data-name=track-total]').text(app.numberFormat(obj.stats.total))
    }

    let __delete = function(__, obj)
    {
        $('input[name=id]:checked').each(function() {
            $(this).closest('.tmp').remove();
        })

        app.etsetraAjax($('#items').data('skip', 0))
    }
@endpush

@push('footer')
    <div
        id="createModal"
        class="modal"
        data-bs-backdrop="static"
        aria-hidden="true"
        aria-labelledby="createModalLabel"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow border-0 rounded-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Create Track</h5>
                    <button
                        type="button"
                        class="btn-close rounded-circle"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form
                        id="createForm"
                        autocomplete="off"
                        method="post"
                        action="#"
                        data-blockui="#createModal->find(.modal-content)"
                        data-callback="__create"
                        data-action="{{ route('api.track.create') }}">
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-floating flex-fill mb-2">
                                <select class="form-select shadow-sm rounded-0" name="source" id="source">
                                    <option value="">Choose</option>
                                    @foreach (config('sources') as $key => $source)
                                        <option value="{{ $key }}" data-types='{!! json_encode(array_keys($source['tracks'])) !!}'>{{ $source['name'] }}</option>
                                    @endforeach
                                </select>
                                <label for="source">{{ __('validation.attributes.source') }}</label>
                            </div>
                            <div class="form-floating flex-fill mb-2">
                                <select disabled class="form-select shadow-sm rounded-0" name="type" id="type"></select>
                                <label for="type">{{ __('validation.attributes.type') }}</label>
                            </div>
                        </div>
                        <div class="form-floating mb-2">
                            <input disabled type="text" class="form-control shadow-sm rounded-0" name="value" id="value" />
                            <label for="value">{{ __('validation.attributes.value') }}</label>
                            <small class="text-muted">Accepted value properties:</small>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item text-muted p-0 small d-flex align-items-center gap-2 border-0"><i class="material-icons icon-sm">info</i> <span>Keyword, domain, profile, hashtag, url etc.</span></li>
                                <li class="list-group-item text-muted p-0 small d-flex align-items-center gap-2 border-0"><i class="material-icons icon-sm">info</i> <span>Value differs depending on the source.</span></li>
                                <li class="list-group-item text-muted p-0 small d-flex align-items-center gap-2 border-0"><i class="material-icons icon-sm">info</i> <span>For <strong>hashtag</strong> type, do not put a square at the beginning.</span></li>
                                <li class="list-group-item text-muted p-0 small d-flex align-items-center gap-2 border-0"><i class="material-icons icon-sm">info</i> <span>Specify only the <strong>domain</strong> for the news site.</span></li>
                            </ul>
                        </div>
                        <div class="alert alert-warning rounded-0 shadow-sm mb-4 d-flex align-items-center gap-3">
                            <i class="material-icons">warning</i>
                            <small>Automatic verification is done every 10 minutes. The criteria you add will be verified and tracked within 10 minutes.</small>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-outline-secondary shadow-sm rounded-0">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@section('content')
    <div class="card rounded-0 shadow-sm" id="masterCard">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row">
                <div class="d-flex gap-2 me-auto mb-1">
                    <span class="card-title text-uppercase h6 fw-bold mb-0">Feeds</span>
                    <small class="text-muted">
                        Total <span data-name="track-total">0</span>
                    </small>
                </div>
                <div class="d-flex gap-1">
                    <input
                        type="text"
                        class="form-control form-control-sm shadow-sm rounded-0 bg-light px-3"
                        name="search"
                        data-blockui="#masterCard"
                        data-reset="true"
                        data-action="true"
                        data-action-target="#items"
                        placeholder="Search" />
                    <a
                        href="#"
                        class="btn btn-outline-danger btn-sm shadow-sm rounded-0"
                        data-name="drop"
                        data-include="id"
                        data-action="{{ route('feed.delete') }}"
                        data-blockui="#masterCard"
                        data-callback="__delete"
                        data-confirmation="Do you want to delete the selected records?"
                        title="Delete">
                        <i class="material-icons icon-sm">delete</i>
                    </a>
                </div>
            </div>
        </div>
        <div
            id="items"
            class="list-group list-group-flush load border-0"
            data-action="{{ route('feed.list') }}"
            data-callback="__results"
            data-skip="0"
            data-take="10"
            data-include="search"
            data-loading="#items->children(.loading)"
            data-more="#itemsMore"
            data-each="#items">
            <div class="list-group-item border-0 d-flex justify-content-center loading">
                <img alt="Loading" src="{{ asset('images/rolling-dark.svg') }}" class="w-32px h-32px" />
            </div>
            <label class="list-group-item list-group-item-action border-0 each-model unselectable">
                <div class="d-flex align-items-center gap-2">
                    <div class="form-check d-flex align-items-center">
                        <input
                            class="form-check-input rounded-0 shadow-sm"
                            data-multiple="true"
                            type="checkbox"
                            name="id"
                            value="0"
                            data-col="id" />
                    </div>
                    <div class="d-flex align-items-start align-items-sm-center justify-content-between flex-column flex-sm-row flex-fill">
                        <span data-col="name"></span>
                        <div class="btn-group d-flex gap-2">
                            <a title="JSON" href="#" class="link-success small" target="_blank" data-name="json">JSON</a>
                            <a title="XML" href="#" class="link-primary small" target="_blank" data-name="xml">XML</a>
                        </div>
                    </div>
                </div>
            </label>
        </div>
        <a
            href="#"
            id="itemsMore"
            class="d-none py-1"
            data-blockui="#masterCard"
            data-action="true"
            data-action-target="#items"
            title="More">
            <i class="material-icons d-table mx-auto text-muted">more_horiz</i>
        </a>
    </div>
    <div class="py-4 text-muted d-flex align-items-center justify-content-center gap-2">
        <i class="material-icons">info</i>
        <small>Feeds are updated every <strong>15 minutes</strong>, up to 1000 items are shown at a time.</small>
    </div>
@endsection
