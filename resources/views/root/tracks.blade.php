@extends(
    'layouts.master',
    [
        'title' => 'Track Management',
        'master' => true,
        'breadcrumb' => [
            'Dashboard' => route('dashboard'),
            'Root' => route('dashboard'),
            'Track Management' => '#'
        ]
    ]
)

@push('js')
    let __items = function(__, o)
    {
        __.find('[data-name=edit]').data('id', o.id)

        __.find('[data-name=valid]')
            .removeClass('text-danger text-success text-warning')
            .addClass(o.valid === true ? 'text-success' : o.valid === false ? 'text-danger' : 'text-info')
            .html(o.valid === true ? 'Valid' : o.valid === false ? 'Invalid' : 'Pending')
    }

    let __results = function(__, obj)
    {
        $('[data-name=total-count]').text(app.numberFormat(obj.stats.total))

        let drop = $('[data-name=drop]');
        	drop.addClass('d-none').removeClass(obj.data.length ? 'd-none' : '')
    }

    let __delete = function(__, obj)
    {
        $('input[name=id]:checked').each(function() {
            $(this).closest('.tmp').remove();
        })

        app.etsetraAjax($('#items').data('skip', 0))
    }

    let edit = function(__, obj)
    {
        let modal = $('#editModal');
            modal.find('input[name=reason]').val(obj.data.error_reason)
            modal.find('form').data('id', obj.data.id)
            modal.modal('show')
    }

    let __edit = function(__, obj)
    {
        $('#editModal').modal('hide')
    }
@endpush

@push('footer')
    <div
        id="editModal"
        class="modal"
        data-bs-backdrop="static"
        aria-hidden="true"
        aria-labelledby="editModalLabel"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow border-0 rounded-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Error Reason</h5>
                    <button
                        type="button"
                        class="btn-close rounded-circle"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form
                        id="editForm"
                        autocomplete="off"
                        method="post"
                        action="#"
                        data-blockui="#editModal->find(.modal-content)"
                        data-callback="__edit"
                        data-action="{{ route('root.tracks.update') }}">

                        <div class="form-floating mb-4">
                            <input type="text" class="form-control shadow-sm rounded-0" name="reason" id="reason" />
                            <label for="reason">{{ __('validation.attributes.reason') }}</label>
                            <small class="text-muted">The tracks for which the reason is specified are not followed.</small>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-outline-secondary shadow-sm rounded-0">Save</button>
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
                <div class="d-flex gap-2 me-auto mb-2">
                    <span class="card-title text-uppercase h6 fw-bold mb-0">Track Management</span>
                    <small class="text-muted">
                        Total <span data-name="total-count">0</span>
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
                        class="btn btn-outline-danger btn-sm rounded-0 d-none"
                        data-name="drop"
                        data-include="id"
                        data-action="{{ route('root.tracks.delete') }}"
                        data-blockui="#masterCard"
                        data-callback="__delete"
                        data-confirmation="Do you want to delete the selected records?">
                        <i class="material-icons icon-sm">delete</i>
                    </a>
                </div>
            </div>
        </div>
        <div
            id="items"
            class="list-group list-group-flush load border-0"
            data-action="{{ route('root.tracks.list') }}"
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
                    <div class="d-flex flex-column">
                        <small class="d-flex gap-1">
                            <span data-col="source" class="fw-bold"></span>
                            <span data-col="type" class="text-muted"></span>
                        </small>
                        <span data-col="value"></span>
                    </div>
                    <div class="ms-auto">
                        <small class="text-end d-block" data-name="valid"></small>
                        <span class="text-end d-block text-muted">Every <span data-col="request_frequency"></span> min / <span data-col="request_hit"></span></span>
                        <a
                            href="#"
                            class="text-end d-block"
                            data-name="edit"
                            data-blockui="#masterCard"
                            data-callback="edit"
                            data-action="{{ route('root.tracks.read') }}">
                            <span data-col="error_hit"></span> <span>error</span>
                        </a>
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
            data-action-target="#items">
            <i class="material-icons d-table mx-auto text-muted">more_horiz</i>
        </a>
    </div>
@endsection
