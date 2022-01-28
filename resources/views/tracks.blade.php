@extends(
    'layouts.master',
    [
        'title' => 'Track API\'s',
        'master' => true,
        'breadcrumb' => [
            'Dashboard' => route('dashboard'),
            'Track API\'s' => '#'
        ]
    ]
)

@push('css')
    .source-selectors > .btn {
        border-width: 0 0 2px 0;
        box-shadow: none !important;
    }
    .source-selectors > .btn:hover {
        border-color: #6c757d;
    }
    .source-selectors > .btn-check:checked + .btn,
    source-selectors > .btn-check:active + .btn {
        border-color: #6c757d;
        background-color: #f8f9fa;
    }
@endpush

@push('js')
    const driver = new app.Driver();

    let __items = function(__, o)
    {
        __.find('[data-name=valid]')
            .removeClass('text-danger text-success text-warning')
            .addClass(o.valid === true ? 'text-success' : o.valid === false ? 'text-danger' : 'text-info')
            .html(o.valid === true ? 'Valid' : o.valid === false ? 'Invalid' : 'Pending')
    }

    let __results = function(__, obj)
    {
        $('[data-name=track-total]').text(app.numberFormat(obj.track.total))
        $('[data-name=track-limit]').text(app.numberFormat(obj.track.limit))

        let drop = $('[data-name=drop]');
        let create = $('[data-name=create]');

        drop.addClass('d-none').removeClass(obj.data.length ? 'd-none' : '')
        create.addClass('d-none').removeClass(obj.track.total < obj.track.limit ? 'd-none' : '')

        if (obj.track.total == 0)
        {
            driver.highlight({
                element: '[data-name=create]',
                popover: {
                    title: 'First, create the criteria you want to track',
                    position: 'left',
                    showButtons: false,
                }
            })
        }
        else
        {
            app.info('track.list', function() {
                driver.highlight({
                    element: '.accordion-header',
                    popover: {
                        title: 'Click here for API document',
                        position: 'top',
                        showButtons: false,
                    }
                })
            }, true)
        }
    }

    let sourceUpdate = function()
    {
        let form = $('#createForm');
        let source = form.find('select[name=source]');
        let type = form.find('select[name=type]');
            type.html('')
        let value = form.find('input[name=value]');

        type.append($('<option />', { 'value': '', 'text': 'Choose' }))

        $.each(source.children('option:selected').data('types'), function(k, value) {
            type.append($('<option />', { 'value': value, 'text': value }))
        })

            type.removeAttr('disabled')
            value.removeAttr('disabled')
    }

    $(window).on('load', sourceUpdate)
    $(document).on('change', 'select[name=source]', sourceUpdate)

    let __create = function(__, obj)
    {
        $('#createModal').modal('hide')

        let items = $('#items');
            items.find('.tmp').remove();

        app.etsetraAjax(items.data('skip', 0))
    }

    let __delete = function(__, obj)
    {
        $('input[name=id]:checked').each(function() {
            $(this).closest('.tmp').remove();
        })

        app.etsetraAjax($('#items').data('skip', 0))
    }

    $(document).on('show.bs.modal','#createModal', function () {
        let form = $(this).find('form');
            form[0].reset()

        sourceUpdate()

        driver.reset()
    }).on('show.bs.collapse','#apiAccordion', function () {
        driver.reset()
    })
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
                    <span class="card-title text-uppercase h6 fw-bold mb-0">Track API's</span>
                    <small class="text-muted">
                        Current <span data-name="track-total">0</span> / Limit <span data-name="track-limit">0</span>
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
                        class="btn btn-outline-success btn-sm shadow-sm rounded-0 d-none"
                        data-name="create"
                        data-bs-toggle="modal"
                        data-bs-target="#createModal"
                        title="Create">
                        <i class="material-icons icon-sm">add</i>
                    </a>
                    <a
                        href="#"
                        class="btn btn-outline-danger btn-sm shadow-sm rounded-0 d-none"
                        data-name="drop"
                        data-include="id"
                        data-action="{{ route('api.track.delete') }}"
                        data-blockui="#masterCard"
                        data-callback="__delete"
                        data-confirmation="Do you want to delete the selected records?"
                        title="Delete">
                        <i class="material-icons icon-sm">delete</i>
                    </a>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-start btn-group source-selectors gap-1"> 
            @foreach (config('sources') as $key => $item)
                <input
                    type="checkbox"
                    class="btn-check"
                    name="sources"
                    checked
                    data-alias="source"
                    data-multiple="true"
                    data-blockui="#masterCard"
                    data-reset="true"
                    data-action="true"
                    data-action-target="#items"
                    id="{{ $key }}"
                    value="{{ $key }}">
                <label class="btn rounded-0 mx-0 d-flex flex-column flex-md-row align-items-center justify-content-center gap-0 gap-md-2 py-2" for="{{ $key }}">
                    <img alt="{{ $key }}" src="{{ asset($item['icon']) }}" class="w-24px h-24px" />
                    <small>{{ $item['name'] }}</small>
                </label>
            @endforeach
        </div>
        <div
            id="items"
            class="list-group list-group-flush load border-0"
            data-action="{{ route('api.track.list') }}"
            data-callback="__results"
            data-skip="0"
            data-take="10"
            data-include="search,sources"
            data-loading="#items->children(.loading)"
            data-headers='{"X-Api-Key": "{{ auth()->user()->api_key }}", "X-Api-Secret": "{{ auth()->user()->api_secret }}"}'
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
                    <div class="d-flex flex-column">
                        <small class="d-flex gap-1">
                            <span data-col="source" class="fw-bold"></span>
                            <span data-col="type" class="text-muted"></span>
                        </small>
                        <span data-col="value"></span>
                    </div>
                    <div class="ms-auto">
                        <span class="text-end d-flex align-items-center justify-content-end gap-1">
                            <small data-name="valid"></small>
                        </span>
                        <span class="text-end d-flex align-items-center justify-content-end text-muted" title="Last 6 hours">
                            <small data-col="total_data">0</small><small class="pe-2">+</small> <small>data in 6 hours</small>
                        </span>
                    </div>
                </div>
                <p class="text-danger mb-0" data-col="error_reason"></p>
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
        @component('includes.api')
            @slot('apis', $apis)
            @slot('rate_limit', $rate_limit)
            @slot('rate_minutes', $rate_minutes)
        @endcomponent
    </div>
@endsection
