@extends(
    'layouts.master',
    [
        'title' => 'User Management',
        'master' => true,
        'breadcrumb' => [
            'Dashboard' => route('dashboard'),
            'Root' => route('dashboard'),
            'Sessions' => [
                'User Management' => route('root.users'),
            ],
        ]
    ]
)

@push('js')
    let __results = function(__, obj)
    {
        $('[data-name=total-count]').text(app.numberFormat(obj.stats.total))
        $('[data-name=online-count]').text(app.numberFormat(obj.stats.online))

        let drop = $('[data-name=drop]');
        	drop.addClass('d-none').removeClass(obj.data.length ? 'd-none' : '')

        $.each(__.find('.tmp'), function() {
            let ___ = $(this);

            if (___.data('ts') >= obj.ts)
                ___.addClass('bg-success bg-opacity-10')
        })
    }

    let __items = function(__, o)
    {
        if (o.user)
        {
            let user = __.find('[data-name=user]');
                user.addClass('d-none').removeClass(o.user ? 'd-none' : '')
                user.find('small').html(o.user.name)
        }

        __.data('ts', o.last_activity)
    }
@endpush

@section('content')
    <div class="card rounded-0 shadow-sm" id="masterCard">
        <div class="card-body">
            <div class="d-flex gap-2">
                <div class="d-flex gap-2 me-auto mb-1">
                    <span class="card-title text-uppercase h6 fw-bold mb-0">Sessions</span>
                    <small class="text-muted">
                        Total <span data-name="total-count">0</span> / Online <span data-name="online-count">0</span>
                    </small>
                </div>
            </div>
        </div>
        <div
            id="items"
            class="list-group list-group-flush load border-0"
            data-action="{{ route('root.users.sessions') }}"
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
                <div>
                    <span class="fw-bold" data-col="ip_address"></span>
                    <span data-name="user" class="d-none"><small></small></span>
                </div>
                <small class="text-muted" data-col="user_agent"></small>
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
