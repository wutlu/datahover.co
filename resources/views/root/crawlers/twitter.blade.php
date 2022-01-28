@extends(
    'layouts.master',
    [
        'title' => 'Twitter Settings',
        'master' => true,
        'breadcrumb' => [
            'Dashboard' => route('dashboard'),
            'Root' => route('dashboard'),
            'Twitter Settings' => '#',
        ]
    ]
)

@push('js')
    let __items = function(__, o)
    {
        let status = __.find('[data-name=status]').removeClass('text-success text-muted text-danger text-info text-warning');
            status.html(o.status)

        switch (o.status)
        {
            case 'working': status.addClass('text-success'); break;
            case 'not_working': status.addClass('text-muted'); break;
            case 'error': status.addClass('text-danger'); break;
            case 'restart': status.addClass('text-info'); break;
            case 'stop': status.addClass('text-danger'); break;
            case 'start': status.addClass('text-warning'); break;
        }
    }

    let __results = function(__, obj)
    {
        $('[data-name=total]').text(app.numberFormat(obj.stats.total))

        let drop = $('[data-name=drop]');

        drop.addClass('d-none').removeClass(obj.data.length ? 'd-none' : '')
    }

    let __create = function(__, obj)
    {
        $('#createModal').modal('hide')
    }

    let __status = function(__, obj)
    {
        $('#statusModal').modal('hide')
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
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content shadow border-0 rounded-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Create Token</h5>
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
                        data-action="{{ route('crawlers.twitter.tokens.create') }}">

					    <div class="form-floating mb-2">
					    	<input type="text" class="form-control rounded-0" name="screen_name" id="screen_name" />
					    	<label for="screen_name">Screen Name</label>
					    </div>
					    <div class="form-floating mb-2">
					    	<input type="password" class="form-control rounded-0" name="password" id="password" />
					    	<label for="password">Password</label>
					    </div>
				    	<small class="text-muted mb-4 d-block">Connect to your Twitter account. There may be several accounts that appear to be linked in your account. Please do not turn them off.</small>

                        <button type="submit" class="btn btn-outline-secondary shadow-sm rounded-0 w-100">Connect and Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div
        id="statusModal"
        class="modal"
        data-bs-backdrop="static"
        aria-hidden="true"
        aria-labelledby="statusModalLabel"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content shadow border-0 rounded-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Bot Status</h5>
                    <button
                        type="button"
                        class="btn-close rounded-circle"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form
                        id="statusForm"
                        autocomplete="off"
                        method="post"
                        action="#"
                        data-key="twitter.status"
                        data-blockui="#statusModal->find(.modal-content)"
                        data-callback="__status"
                        data-action="{{ route('option.update') }}">

                        <div class="form-floating mb-4">
                            <select class="form-select shadow-sm rounded-0" name="value" id="value">
                                <option value="on" {{ $status == 'on' ? 'selected' : '' }}>On</option>
                                <option value="off" {{ $status == 'off' ? 'selected' : '' }}>Off</option>
                            </select>
                            <label for="value">{{ __('validation.attributes.value') }}</label>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-outline-secondary shadow-sm rounded-0">Update</button>
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
                    <span class="card-title text-uppercase h6 fw-bold mb-0">Twitter Tokens</span>
                    <small class="text-muted">
                        Total <span data-name="total">0</span>
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
                        placeholder="Screen Name" />
                    <a
                        href="#"
                        class="btn btn-outline-success btn-sm shadow-sm rounded-0"
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
                        data-action="{{ route('crawlers.twitter.tokens.delete') }}"
                        data-blockui="#masterCard"
                        data-callback="__delete"
                        data-confirmation="Do you want to delete the selected records?"
                        title="Delete">
                        <i class="material-icons icon-sm">delete</i>
                    </a>
                    <a
                        href="#"
                        class="btn btn-outline-secondary btn-sm shadow-sm rounded-0"
                        data-name="status"
                        data-bs-toggle="modal"
                        data-bs-target="#statusModal"
                        title="Settings">
                        <i class="material-icons icon-sm">settings</i>
                    </a>
                </div>
            </div>
        </div>
        <div
            id="items"
            class="list-group list-group-flush load border-0"
            data-action="{{ route('crawlers.twitter.tokens') }}"
            data-callback="__results"
            data-skip="0"
            data-take="10"
            data-include="search,sources"
            data-loading="#items->children(.loading)"
            data-more="#itemsMore"
            data-each="#items">
            <div class="list-group-item border-0 d-flex justify-content-center loading">
                <img alt="Loading" src="{{ asset('images/rolling-dark.svg') }}" class="w-32px h-32px" />
            </div>
            <label class="list-group-item list-group-item-action border-0 each-model unselectable">
                <div class="d-flex gap-2">
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
                        <span>
                            <span class="text-muted">User</span>
                            <span data-col="screen_name" class="fw-bold"></span>
                            <small class="text-muted">
                                (<span data-col="device"></span>)
                            </small>
                        </span>
                        <span>
                            <span class="text-muted">Pass</span>
                            <span data-col="password" class="blur-2 blur-hover"></span>
                        </span>
                    </div>
                    <div class="ms-auto">
                        <small class="text-end d-block text-muted">
                            <span data-col="error_hit"></span>
                            error
                        </small>
                        <small class="text-end d-block text-muted" data-col="type"></small>
                        <small class="text-end d-block" data-name="status"></small>
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
    </div>
@endsection
