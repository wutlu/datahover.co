@extends(
    'layouts.master',
    [
        'title' => 'Proxy Management',
        'master' => true,
        'breadcrumb' => [
            'Dashboard' => route('dashboard'),
            'Root' => route('dashboard'),
            'Proxy Management' => '#',
        ]
    ]
)

@push('js')
    let __items = function(__, o)
    {
    	__.find('[data-name=ip]')
    	  .val(o.username + ':' + o.password + '@' + o.ip + ':' + o.port)
    	  .attr('id', 'input-' + o.id)
    	  .attr('data-copy', 'input-' + o.id)

        let progress = __.find('[data-name=progress-bar]')
                         .children('.progress-bar')
                         .css({ 'width': (o.speed * 10) + '%' })
                         .removeClass('bg-info bg-success bg-warning bg-danger')

        switch(o.speed)
        {
            case 0:
            case 1:
            case 2:
                progress.addClass('bg-danger');
            break;
            case 3:
            case 4:
            case 5:
                progress.addClass('bg-warning');
            break;
            case 6:
            case 7:
            case 8:
                progress.addClass('bg-info');
            break;
            case 9:
            case 10:
                progress.addClass('bg-success');
            break;
        }
    }

    let __results = function(__, obj)
    {
    }

    let __settings = function(__, obj)
    {
        $('#settingsModal').modal('hide')
    }
@endpush

@push('footer')
    <div
        id="settingsModal"
        class="modal"
        data-bs-backdrop="static"
        aria-hidden="true"
        aria-labelledby="settingsModalLabel"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow border-0 rounded-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Proxy Settings</h5>
                    <button
                        type="button"
                        class="btn-close rounded-circle"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form
                        id="settingsForm"
                        autocomplete="off"
                        method="post"
                        action="#"
                        data-blockui="#settingsModal->find(.modal-content)"
                        data-callback="__settings"
                        data-action="{{ route('root.proxies.settings') }}">

                        <div class="form-group mb-2">
                            <div class="form-floating">
                                <input type="text" class="form-control rounded-0 shadow-sm" name="api_key" value="{{ $options['proxy.api_key'] }}" />
                                <label for="api_key">Api Key</label>
                            </div>
                            <span class="form-text text-muted">Get an Api Key from <a target="_blank" href="https://proxy6.net">Proxy6.net</a> site.</span>
                        </div>
                        <div class="row mb-2">
                            <div class="col-12 col-sm-6">
                                <div class="form-group mb-2">
                                    <label>Max Buy Piece</label>
                                    <input
                                        type="number"
                                        class="form-control shadow-sm rounded-0"
                                        name="max_buy_piece"
                                        value="{{ $options['proxy.max_buy_piece'] }}"
                                        min="0" />
                                    <span class="text-muted">Maximum number of proxies to keep.</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group mb-2">
                                    <label>Min Balance For Alert</label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text rounded-0">$</span>
                                        <input
                                            type="number"
                                            class="form-control rounded-0"
                                            name="min_balance_for_alert"
                                            value="{{ $options['proxy.min_balance_for_alert'] }}"
                                            min="0" />
                                    </div>
                                    <span class="form-text text-muted">Min <a href="https://proxy6.net">Proxy6.net</a> balance for alert. (Current balance ${{ $options['proxy.current_balance'] }})</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="form-floating mb-2">
                                    <select class="form-select rounded-0 shadow-sm" name="proxy_country">
                                        <option value="ru" {{ $options['proxy.proxy_country'] == 'ru' ? 'selected' : '' }}>Russia</option>
                                        <option value="us" {{ $options['proxy.proxy_country'] == 'us' ? 'selected' : '' }}>United States</option>
                                        <option value="ca" {{ $options['proxy.proxy_country'] == 'ca' ? 'selected' : '' }}>Canada</option>
                                        <option value="de" {{ $options['proxy.proxy_country'] == 'de' ? 'selected' : '' }}>Germany</option>
                                    </select>
                                    <label>Proxy Country</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="form-floating mb-2">
                                    <select class="form-select rounded-0 shadow-sm" name="proxy_version">
                                        <option value="4" {{ $options['proxy.proxy_version'] == 4 ? 'selected' : '' }}>IPv4</option>
                                        <option value="3" {{ $options['proxy.proxy_version'] == 3 ? 'selected' : '' }}>IPv4 Shared</option>
                                        <option value="6" {{ $options['proxy.proxy_version'] == 6 ? 'selected' : '' }}>IPv6</option>
                                    </select>
                                    <label>Proxy Version</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="form-floating mb-2">
                                    <select class="form-select rounded-0 shadow-sm" name="buy_period">
                                        <option value="3" {{ $options['proxy.buy_period'] == '3' ? 'selected' : '' }}>3 day</option>
                                        <option value="7" {{ $options['proxy.buy_period'] == '7' ? 'selected' : '' }}>7 day</option>
                                        <option value="14" {{ $options['proxy.buy_period'] == '14' ? 'selected' : '' }}>14 day</option>
                                        <option value="30" {{ $options['proxy.buy_period'] == '30' ? 'selected' : '' }}>30 day</option>
                                        <option value="60" {{ $options['proxy.buy_period'] == '60' ? 'selected' : '' }}>90 day</option>
                                    </select>
                                    <label>Buy Period</label>
                                </div>
                            </div>
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
            <div class="d-flex flex-column flex-lg-row mb-1">
                <span class="card-title text-uppercase h6 fw-bold me-auto mb-0">Proxies</span>
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
                        class="btn btn-outline-secondary btn-sm shadow-sm rounded-0"
                        data-bs-toggle="modal"
                        data-bs-target="#settingsModal">
                        <i class="material-icons icon-sm">settings</i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div
                id="items"
                class="list-group list-group-flush load"
                data-action="{{ route('root.proxies.list') }}"
                data-callback="__results"
                data-skip="0"
                data-take="25"
                data-include="search,sources"
                data-loading="#items->children(.loading)"
                data-more="#itemsMore"
                data-each="#items">
                <div class="list-group-item border-0 d-flex justify-content-center loading">
                    <img alt="Loading" src="{{ asset('images/rolling-dark.svg') }}" class="w-32px h-32px" />
                </div>
                <div class="list-group-item border-0 each-model unselectable px-0">
                    <div class="d-flex justify-content-between">
                        <small class="text-muted" data-col="type"></small>
                        <small class="text-muted" data-col="expiry_date"></small>
                    </div>
                    <input
                        readonly
                        type="text"
                        data-name="ip"
                        class="form-control shadow-sm rounded-0 w-100" />
                    <div class="progress rounded-0 h-2px" data-name="progress-bar">
                        <div class="progress-bar"></div>
                    </div>
                </div>
            </div>
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
