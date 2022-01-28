@extends(
    'layouts.master',
    [
        'title' => 'FAQ Management',
        'master' => true,
        'breadcrumb' => [
            'Dashboard' => route('dashboard'),
            'Root' => route('dashboard'),
            'FAQ Management' => '#'
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
            modal.find('textarea[name=answer]').val(obj.data.answer)
            modal.find('input[name=question]').val(obj.data.question)
            modal.find('.modal-title').html('Edit Question')
            modal.find('button[type=submit]').html('Save')
            modal.modal('show')
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
                item.find('[data-col=question]').html(obj.data.question)
        }
    }

    $(document).on('click', '[data-trigger=create]', function() {
        let modal = $('#actionModal');
            modal.find('form').removeData('id')
            modal.find('textarea[name=answer]').val('')
            modal.find('input[name=question]').val('')
            modal.find('.modal-title').html('Create Question')
            modal.find('button[type=submit]').html('Create')
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
                        data-action="{{ route('root.faq.action') }}">
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control shadow-sm rounded-0" name="question" id="question" />
                            <label for="question">{{ __('validation.attributes.question') }}</label>
                        </div>
                        <div class="form-floating mb-4">
                            <textarea class="form-control shadow-sm rounded-0" name="answer" id="answer" style="height: 200px;"></textarea>
                            <label for="answer">{{ __('validation.attributes.answer') }}</label>
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
                    <span class="card-title text-uppercase h6 fw-bold mb-0">FAQ Management</span>
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
                        data-action="{{ route('root.faq.delete') }}"
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
            data-action="{{ route('root.faq.list') }}"
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
                    <div data-col="question"></div>
                    <a
                        href="#"
                        class="btn btn-outline-secondary btn-sm shadow-sm rounded-0 ms-auto"
                        data-name="edit"
                        data-blockui="#masterCard"
                        data-callback="edit"
                        data-action="{{ route('root.faq.read') }}"
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
