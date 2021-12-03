@extends(
    'layouts.master',
    [
        'title' => 'User Management',
        'master' => true,
        'breadcrumb' => [
            'Dashboard' => route('dashboard'),
            'Root' => route('dashboard'),
            'User Management' => '#'
        ]
    ]
)

@push('js')
    let __items = function(__, o)
    {
        __.find('[data-name=edit]').data('id', o.id)
    }

    let __results = function(__, obj)
    {
        $('[data-name=total-count]').text(app.numberFormat(obj.stats.total))
        $('[data-name=active-count]').text(app.numberFormat(obj.stats.active))

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
        let modal = $('#edit-modal');
            modal.find('input[name=name]').val(obj.data.name)
            modal.find('input[name=email]').val(obj.data.email)
            modal.find('select[name=plan_id]').val(obj.data.plan_id)
            modal.find('input[name=subscription_end_date]').val(obj.data.subscription_end_date)
            modal.find('input[name=api_key]').val(obj.data.api_key)
            modal.find('input[name=api_secret]').val(obj.data.api_secret)
            modal.find('input[name=is_root]').prop('checked', obj.data.is_root ? true : false)
            modal.find('form').data('id', obj.data.id)
            modal.modal('show')
    }

    let __edit = function(__, obj)
    {
        $('#edit-modal').modal('hide')

        let item = $('#items').find('[data-id=' + obj.data.id + ']');
            item.find('[data-col=name]').html(obj.data.name)
            item.find('[data-col=email]').html(obj.data.email)
    }
@endpush

@push('footer')
    @component('includes.modals.modal', [ 'name' => 'edit', 'title' => 'Edit' ])
        <form
            id="editForm"
            autocomplete="off"
            method="post"
            action="#"
            data-blockui="#edit-modal->find(.modal-content)"
            data-callback="__edit"
            data-action="{{ route('root.users.update') }}">

            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="form-floating mb-2">
                        <input type="text" class="form-control shadow-sm rounded-0" name="name" id="name" />
                        <label for="name">{{ __('validation.attributes.name') }}</label>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-floating mb-2">
                        <input type="email" class="form-control shadow-sm rounded-0" name="email" id="email" />
                        <label for="email">{{ __('validation.attributes.email') }}</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="form-floating mb-2">
                        <select class="form-select shadow-sm rounded-0" name="plan_id" id="plan_id">
                            @foreach($plans as $key => $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                            @endforeach
                        </select>
                        <label for="plan_id">{{ __('validation.attributes.plan_id') }}</label>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-floating mb-2">
                        <input type="text" class="form-control shadow-sm rounded-0" name="subscription_end_date" id="subscription_end_date" />
                        <label for="subscription_end_date">{{ __('validation.attributes.subscription_end_date') }}</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="form-floating mb-2">
                        <input readonly data-copy="api_key" data-copied="Api Key Copied!" class="form-control shadow-sm rounded-0" type="text" name="api_key" id="api_key" />
                        <label for="api_key">{{ __('validation.attributes.api_key') }}</label>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="form-floating mb-2">
                        <input readonly data-copy="api_secret" data-copied="Api Secret Copied!" class="form-control shadow-sm rounded-0" type="text" name="api_secret" id="api_secret" />
                        <label for="api_secret">{{ __('validation.attributes.api_secret') }}</label>
                    </div>
                </div>
            </div>

            <div class="form-check form-switch mb-4">
                <input
                    class="form-check-input rounded-0"
                    type="checkbox"
                    name="is_root"
                    id="is_root"
                    value="on" />
                <label class="form-check-label" for="is_root">Is Root</label>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-outline-secondary shadow-sm rounded-0">Save</button>
            </div>
        </form>
    @endcomponent
@endpush

@section('content')
    <div class="card rounded-0 shadow-sm" id="masterCard">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row">
                <div class="d-flex gap-2 me-auto mb-1">
                    <span class="card-title text-uppercase h6 fw-bold mb-0">User Management</span>
                    <small class="text-muted">
                        Total <span data-name="total-count">0</span> / Active <span data-name="active-count">0</span>
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
                        class="btn btn-outline-danger btn-sm shadow-sm rounded-0 d-none"
                        data-name="drop"
                        data-include="id"
                        data-action="{{ route('root.users.delete') }}"
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
            data-action="{{ route('root.users.list') }}"
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
                    <div class="d-flex align-items-center gap-2">
                    	<img alt="Avatar" data-col="avatar" class="w-48px h-48px rounded-circle" />
                    	<div class="d-flex flex-column">
                            <span data-col="name" class="fw-bold"></span>
                            <small data-col="email" class="text-muted"></small>
                        </div>
                    </div>
                    <a
                        href="#"
                        class="btn btn-outline-secondary btn-sm shadow-sm rounded-0 ms-auto"
                        data-name="edit"
                        data-blockui="#masterCard"
                        data-callback="edit"
                        data-action="{{ route('root.users.read') }}">
                        <i class="material-icons icon-sm">edit</i>
                    </a>
                </div>
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
