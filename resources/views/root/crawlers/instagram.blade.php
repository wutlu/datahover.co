@extends(
    'layouts.master',
    [
        'title' => 'Instagram Settings',
        'master' => true,
        'breadcrumb' => [
            'Dashboard' => route('dashboard'),
            'Root' => route('dashboard'),
            'Instagram Settings' => '#',
        ]
    ]
)

@push('js')
    let __items = function(__, o)
    {
        let status = __.find('[data-col=status]').removeClass('text-success text-danger text-warning');

        switch (o.status)
        {
            case 'normal': status.addClass('text-success'); break;
            case 'authenticate': status.addClass('text-warning'); break;
            case 'error': status.addClass('text-danger'); break;
        }

        __.find('[data-name=edit]').data('id', o.id)
    }

    let __results = function(__, obj)
    {
        $('[data-name=total]').text(app.numberFormat(obj.stats.total))

        let drop = $('[data-name=drop]');

        drop.addClass('d-none').removeClass(obj.data.length ? 'd-none' : '')
    }

    let __action = function(__, obj)
    {
        $('#actionModal').modal('hide')

        app.etsetraAjax($('#items').data('skip', 0))
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

    let __edit = function(__, obj)
    {
        let modal = $('#actionModal');
            modal.find('.modal-title').html('Edit Account')
            modal.find('input[name=id]').val(obj.data.id)
            modal.find('input[name=email]').val(obj.data.email)
            modal.find('input[name=password]').val(obj.data.password)
            modal.find('input[name=sessionid]').val(obj.data.sessionid)
            modal.find('button[type=submit]').html('Update')
            modal.modal('show')
    }

    $(document).on('click', '[data-name=action]', function() {
        let modal = $('#actionModal');
            modal.find('form')[0].reset()
            modal.find('.modal-title').html('Add Account')
            modal.find('button[type=submit]').html('Add')
            modal.modal('show')
    })
@endpush

@push('footer')
    <div
        id="actionModal"
        class="modal"
        data-bs-backdrop="static"
        aria-hidden="true"
        aria-labelledby="actionModalLabel"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content shadow border-0 rounded-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"></h5>
                    <button
                        type="button"
                        class="btn-close rounded-circle"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form
                        id="actionForm"
                        autocomplete="off"
                        method="post"
                        action="#"
                        data-blockui="#actionModal->find(.modal-content)"
                        data-callback="__action"
                        data-action="{{ route('crawlers.instagram.accounts.action') }}">
                        <input type="hidden" name="id" />
					    <div class="form-floating mb-2">
					    	<input type="text" class="form-control rounded-0" name="email" id="email" />
					    	<label for="email">E-mail</label>
					    </div>
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control rounded-0" name="password" id="password" autocomplete="off" />
                            <label for="password">Password</label>
                        </div>
                        <div class="form-floating mb-4">
                            <input type="text" class="form-control rounded-0" name="sessionid" id="sessionid" />
                            <label for="sessionid">Session Id</label>
                        </div>

                        <button type="submit" class="btn btn-outline-secondary shadow-sm rounded-0 w-100"></button>
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
                        data-key="instagram.status"
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
                    <span class="card-title text-uppercase h6 fw-bold mb-0">Instagram Accounts</span>
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
                        placeholder="Search" />
                    <a
                        href="#"
                        class="btn btn-outline-success btn-sm shadow-sm rounded-0"
                        data-name="action">
                        <i class="material-icons icon-sm">add</i>
                    </a>
                    <a
                        href="#"
                        class="btn btn-outline-danger btn-sm shadow-sm rounded-0 d-none"
                        data-name="drop"
                        data-include="id"
                        data-action="{{ route('crawlers.instagram.accounts.delete') }}"
                        data-blockui="#masterCard"
                        data-callback="__delete"
                        data-confirmation="Do you want to delete the selected records?">
                        <i class="material-icons icon-sm">delete</i>
                    </a>
                    <a
                        href="#"
                        class="btn btn-outline-secondary btn-sm shadow-sm rounded-0"
                        data-name="status"
                        data-bs-toggle="modal"
                        data-bs-target="#statusModal">
                        <i class="material-icons icon-sm">settings</i>
                    </a>
                </div>
            </div>
        </div>
        <div
            id="items"
            class="list-group list-group-flush load border-0"
            data-action="{{ route('crawlers.instagram.accounts') }}"
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
                    <div class="w-100">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">E-mail</small>
                            <small data-col="status"></small>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span data-col="email" class="fw-bold"></span>
                            <a
                                href="#"
                                class="link-dark"
                                data-name="edit"
                                data-callback="__edit"
                                data-blockui="#masterCard"
                                data-action="{{ route('crawlers.instagram.accounts.get') }}">Edit</a>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-column align-items-center justify-content-center gap-sm-3 gap-1 flex-sm-row bg-light shadow-sm">
                    <small class="text-muted"><small class="text-dark" data-col="error_hit">0</small> error</small>
                    <small class="text-muted"><small class="text-dark" data-col="request_hit">0</small> request</small>
                    <small class="text-muted"><small class="text-dark" data-col="request_at">0</small> request at</small>
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
