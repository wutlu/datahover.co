@extends(
    'layouts.master',
    [
        'title' => 'YouTube Settings',
        'master' => true,
        'breadcrumb' => [
            'Dashboard' => route('dashboard'),
            'Root' => route('dashboard'),
            'YouTube Settings' => '#',
        ]
    ]
)

@push('js')
    let __status = function(__, obj)
    {
        $('#statusModal').modal('hide')
    }
@endpush

@push('footer')
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
                        data-key="youtube.status"
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
                    <span class="card-title text-uppercase h6 fw-bold mb-0">YouTube Settings</span>
                </div>
                <div class="d-flex gap-1">
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
    </div>
@endsection
