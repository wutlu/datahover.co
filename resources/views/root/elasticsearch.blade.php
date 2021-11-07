@extends(
    'layouts.master',
    [
        'title' => 'Elasticsearch Monitor',
        'master' => true,
        'breadcrumb' => [
            'Dashboard' => route('dashboard'),
            'Root' => route('dashboard'),
            'Elasticsearch Monitor' => '#',
        ]
    ]
)

@push('js')
    let __health = function(__, o)
    {
        app.etsetraAjax($('#nodes'))

        __.find('[data-name=color]').removeClass('bg-warning bg-success bg-danger')

        switch (o.status)
        {
            case 'yellow':
                __.find('[data-name=color]').addClass('bg-warning')
            break;
            case 'red':
                __.find('[data-name=color]').addClass('bg-danger')
            break;
            case 'green':
                __.find('[data-name=color]').addClass('bg-success')
            break;
        }
    }

    let __nodes = function(__, o)
    {
        app.etsetraAjax($('#indices'))

        __.find('[data-name=heap_percent]').children('.progress-bar').css('width', o['heap.percent'] + '%').html(o['heap.percent'] + '%')
        __.find('[data-name=ram_percent]').children('.progress-bar').css('width', o['ram.percent'] + '%').html(o['ram.percent'] + '%')
        __.find('[data-name=cpu]').children('.progress-bar').css('width', o['cpu'] + '%').html(o['cpu'] + '%')
    }

    let __indices = function(__, o)
    {
        __.find('[data-name=color]').removeClass('bg-warning bg-success bg-danger')

        switch (o.health)
        {
            case 'yellow':
                __.find('[data-name=color]').addClass('bg-warning')
            break;
            case 'red':
                __.find('[data-name=color]').addClass('bg-danger')
            break;
            case 'green':
                __.find('[data-name=color]').addClass('bg-success')
            break;
        }
    }
@endpush

@section('content')
    <div class="card rounded-0 shadow-sm mb-4">
        <div class="card-body">
            <span class="card-title text-uppercase h6 fw-bold mb-0">Cluster</span>
        </div>
        <div
            id="health"
            class="load"
            data-action="{{ route('root.elasticsearch.status', 'health') }}"
            data-loading="#health->children(.loading)"
            data-each="#health">
            <div class="card-body d-flex justify-content-center loading">
                <img alt="Loading" src="{{ asset('images/rolling-dark.svg') }}" class="w-32px h-32px" />
            </div>
            <div class="each-model">
                <div class="h-4px" data-name="color"></div>
                <div class="card-header shadow-sm">
                    <h4 class="card-label font-weight-bolder font-size-h4 mb-2" data-col="cluster"></h4>
                    <span class="font-weight-bold">
                        <abbr title="Max Task Wait Time" data-bs-toggle="tooltip" data-bs-placement="right">MTWT</abbr>
                        <span data-col="max_task_wait_time">-</span>
                    </span>
                    <div>
                        <small class="text-muted">Total Node</small> <small data-col="node.total">-</small>
                        <span class="text-muted">/</span>
                        <small class="text-muted">Data Nodes</small> <small data-col="node.data">-</small>
                    </div>
                </div>
                <div class="p-3 d-flex align-items-end">
                    <div class="flex-grow-1">
                        <div class="mb-2">
                            <div class="fw-bold text-muted">Active Shards Percent</div>
                            <small data-col="active_shards_percent">-</small>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>
                                <small class="d-table text-muted">Shards</small>
                                <span data-col="shards">-</span>
                            </span>
                            <span>
                                <small class="d-table text-muted">Pri</small>
                                <span data-col="pri">-</span>
                            </span>
                            <span>
                                <small class="d-table text-muted">Relo</small>
                                <span data-col="relo">-</span>
                            </span>
                            <span>
                                <small class="d-table text-muted">Init</small>
                                <span data-col="init">-</span>
                            </span>
                            <span>
                                <small class="d-table text-muted">Unassign</small>
                                <span data-col="unassign">-</span>
                            </span>
                            <span>
                                <small class="d-table text-muted">Pending Tasks</small>
                                <span data-col="pending_tasks">-</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ul
            id="nodes"
            class="list-group list-group-flush"
            data-action="{{ route('root.elasticsearch.status', 'nodes') }}"
            data-each="#nodes">
            <li class="list-group-item each-model">
                <div class="d-flex flex-column mb-2">
                    <h6 class="mb-0">
                        <span data-col="name"></span>
                        <span data-col="master"></span>
                    </h6>
                    <small class="text-muted" data-col="ip"></small>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <small class="text-muted">CPU</small>
                        <div class="progress rounded-0 h-10px" data-name="cpu">
                            <div class="progress-bar small"></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <small class="text-muted">Heap Percent</small>
                        <div class="progress rounded-0 h-10px" data-name="heap_percent">
                            <div class="progress-bar small"></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <small class="text-muted">Ram Percent</small>
                        <div class="progress rounded-0 h-10px" data-name="ram_percent">
                            <div class="progress-bar small"></div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="card rounded-0 shadow-sm">
        <div class="card-body">
            <span class="card-title text-uppercase h6 fw-bold mb-0">All Indices</span>
        </div>
        <div
            id="indices"
            data-action="{{ route('root.elasticsearch.status', 'indices') }}"
            data-loading="#indices->children(.loading)"
            data-each="#indices">
            <div class="card-body d-flex justify-content-center loading">
                <img alt="Loading" src="{{ asset('images/rolling-dark.svg') }}" class="w-32px h-32px" />
            </div>
            <div class="each-model">
                <div class="h-4px" data-name="color"></div>
                <div class="card-header shadow-sm">
                    <h6 class="card-label font-weight-bolder font-size-h4 mb-0" data-col="index"></h6>
                    <div>
                        <small class="text-muted">Private Shard</small> <small data-col="pri">-</small>
                        <span class="text-muted">/</span>
                        <small class="text-muted">Replica Shard</small> <small data-col="rep">-</small>
                    </div>
                </div>
                <div class="p-3 d-flex align-items-end">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <span>
                                <small class="d-table text-muted">Docs Count</small>
                                <span data-col="docs.count">-</span>
                            </span>
                            <span>
                                <small class="d-table text-muted">Docs Deleted</small>
                                <span data-col="docs.deleted">-</span>
                            </span>
                            <span>
                                <small class="d-table text-muted">Store Size</small>
                                <span data-col="store.size">-</span>
                            </span>
                            <span>
                                <small class="d-table text-muted">Private Store Size</small>
                                <span data-col="pri.store.size">-</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
