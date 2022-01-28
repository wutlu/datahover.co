@extends(
    'layouts.master',
    [
        'title' => 'Plan Management',
        'master' => true,
        'breadcrumb' => [
            'Dashboard' => route('dashboard'),
            'Root' => route('dashboard'),
            'Plan Management' => '#'
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
        let modal = $('#actionModal');
            modal.find('form').data('id', obj.data.id)
            modal.find('input[name=name]').val(obj.data.name)
            modal.find('input[name=tracks]').val(obj.data.track_limit)
            modal.find('input[name=price]').val(parseInt(obj.data.price))
            modal.find('.modal-title').html('Edit Plan')
            modal.find('button[type=submit]').html('Save')
            modal.modal('show')

        if (obj.data.user)
        {
            let newOption = new Option(obj.data.user.name + ' (' + obj.data.user.email + ')', obj.data.user_id, true, true);
            modal.find('select[name=user_id]').append(newOption).trigger('change')
        }
    }

    let __action = function(__, obj)
    {
        let modal = $('#actionModal').modal('hide')
        let items = $('#items');

        if (obj.type == 'create')
        {
            items.find('.tmp').remove();

            app.etsetraAjax(items.data('skip', 0))
        }
        else
        {
            let item = items.find('[data-id=' + obj.data.id + ']');
                item.find('[data-col=name]').html(obj.data.name)
        }
    }

    $(document).on('click', '[data-trigger=create]', function() {
        let modal = $('#actionModal');
            modal.find('form').removeData('id')
            modal.find('input[name=name]').val('')
            modal.find('.modal-title').html('Create Plan')
            modal.find('button[type=submit]').html('Create')
            modal.modal('show')
    })

    $(document).ready(function() {
        $('select[name=user_id]').select2(
            {
                dropdownParent: $('#actionModal'),
                theme: 'bootstrap-5',
                ajax: {
                    url: '{{ route('root.users.list') }}',
                    dataType: 'json',
                    type: 'POST',
                    data: function(params) {
                        let query = {
                            search: params.term,
                            skip: 0,
                            take: 10
                        }

                        return query;
                    },
                    processResults: function(source) {
                        var result = $.map(source.data, function (item) {
                            return {
                                id: item.id,
                                text: item.name + ' (' + item.email + ')'
                            };
                        });
                        return { results: result };
                    },
                    delay: 250
                },
                allowClear: true,
                placeholder: 'Private Plan'
            }
        );
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
        <div class="modal-dialog modal-dialog-centered" role="document">
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
                        data-action="{{ route('root.plans.action') }}">
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control shadow-sm rounded-0" name="name" id="name" />
                            <label for="name">{{ __('validation.attributes.name') }}</label>
                            <small class="invalid-feedback"></small>
                        </div>
                        <div class="form-floating mb-2">
                            <input type="number" class="form-control shadow-sm rounded-0" name="tracks" id="tracks" value="0" min="0" />
                            <label for="tracks">{{ __('validation.attributes.tracks') }}</label>
                            <small class="invalid-feedback"></small>
                        </div>
                        <div class="form-group mb-2">
                            <small class="text-muted">Montly Price</small>
                            <div class="input-group flex-nowrap border border-1 shadow-sm">
                                <span class="input-group-text border-0 rounded-0 bg-transparent pe-0">{{ config('cashier.currency_symbol') }}</span>
                                <input
                                    type="number"
                                    name="price"
                                    id="price"
                                    value="0"
                                    min="0"
                                    class="form-control m-0 shadow-none border-0"
                                    placeholder="Monthly Price" />
                                <span class="input-group-text border-0 rounded-0 bg-transparent">.00</span>
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <small class="text-muted">Private Plan</small>
                            <select name="user_id" id="user_id"></select>
                            <small class="invalid-feedback"></small>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-outline-secondary shadow-sm rounded-0"></button>
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
                    <span class="card-title text-uppercase h6 fw-bold mb-0">Plan Management</span>
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
                        class="btn btn-outline-success btn-sm shadow-sm rounded-0"
                        data-trigger="create"
                        title="Create">
                        <i class="material-icons icon-sm">add</i>
                    </a>
                    <a
                        href="#"
                        class="btn btn-outline-danger btn-sm shadow-sm rounded-0 d-none"
                        data-name="drop"
                        data-include="id"
                        data-action="{{ route('root.plans.delete') }}"
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
            data-action="{{ route('root.plans.list') }}"
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
                    <div data-col="name"></div>
                    <a
                        href="#"
                        class="btn btn-outline-secondary btn-sm shadow-sm rounded-0 ms-auto"
                        data-name="edit"
                        data-blockui="#masterCard"
                        data-callback="edit"
                        data-action="{{ route('root.plans.read') }}"
                        title="Edit">
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
            data-action-target="#items"
            title="More">
            <i class="material-icons d-table mx-auto text-muted">more_horiz</i>
        </a>
    </div>
@endsection
