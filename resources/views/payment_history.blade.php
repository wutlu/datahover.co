@extends(
    'layouts.master',
    [
        'title' => 'Payment History',
        'master' => true,
        'breadcrumb' => [
            'Dashboard' => route('dashboard'),
            'Manage Subscription' => route('subscription.index'),
            'Payment History' => '#'
        ]
    ]
)

@push('js')
    let __items = function(__, o)
    {
        let status = __.find('[data-name=status]');

        if (o.status == null)
            status.html('Pending').addClass('text-muted');
        else if (o.status == true)
            status.html('Paid').addClass('text-success');
        else
            status.html('Failed').addClass('text-danger');

        let amount = __.find('[data-name=amount]');

        if (o.amount == 0)
            amount.addClass('text-dark')
        else if (o.amount > 0)
            amount.addClass('text-dark')
        else if (o.amount < 0)
            amount.addClass('text-dark')

        if (o.session_id)
        {
            if (o.status == null)
            {
                __.find('[data-name=pay]')
                  .removeClass('d-none')
                  .addClass('d-flex')
                  .attr('href', o.meta.url)
            }

            if (o.meta.url)
            {
                let invoice_link = '{{ route('invoice', [ 'key' => '__key__' ]) }}';
                    invoice_link = invoice_link.replace('__key__', o.session_id)

                __.find('[data-name=invoice]')
                  .removeClass('d-none')
                  .addClass('d-flex')
                  .attr('href', invoice_link)
            }
        }

        __.find('[data-name=payload]').data('payload', o.meta)
    }

    let __results = function(__, obj)
    {
        $('[data-name=total-count]').text(app.numberFormat(obj.stats.total))
    }
@endpush

@push('footer')
    @component('includes.modals.modal')
        @slot('title', 'Payload (Payment Metas)')
        @slot('name', 'payload')
        <pre class="payload mb-0"></pre>
    @endcomponent
@endpush

@push('js')
    $(window).on('load', function() {
        $('#payload-modal').on('shown.bs.modal', function (e) {
            let __ = $(this);
            let invoker = $(e.relatedTarget);
            let payload = JSON.stringify(invoker.data('payload'), null, 2);

            __.find('.payload').html(payload)
            __.find('.modal-body').css({ 'background-color': '#d6bc6f' })
        })
    })
@endpush

@section('content')
    <div class="card rounded-0 shadow-sm" id="masterCard">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row">
                <div class="d-flex gap-2 me-auto mb-1">
                    <span class="card-title text-uppercase h6 fw-bold mb-0">Payment History</span>
                    <small class="text-muted">
                        Total <span data-name="total-count">0</span>
                    </small>
                </div>
                <div class="d-flex gap-1">
                    <!---->
                </div>
            </div>
        </div>
        <div
            id="items"
            class="list-group list-group-flush load border-0"
            data-action="{{ route('payment.history') }}"
            data-callback="__results"
            data-skip="0"
            data-take="10"
            data-loading="#items->children(.loading)"
            data-more="#itemsMore"
            data-each="#items">
            <div class="list-group-item border-0 d-flex justify-content-center loading">
                <img alt="Loading" src="{{ asset('images/rolling-dark.svg') }}" class="w-32px h-32px" />
            </div>
            <label class="list-group-item list-group-item-action border-0 each-model unselectable">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex flex-column">
                        <span data-name="amount">
                            {{ config('cashier.currency_symbol') }} <span data-col="amount"></span>
                        </span>
                        <small data-name="status"></small>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <small class="text-muted" data-col="created_at"></small>
                        <div class="d-flex align-items-center justify-content-end gap-3">
                            <a data-name="pay" href="#" class="small link-dark d-none align-items-center gap-2" target="_blank">
                                <i class="material-icons icon-sm">credit_card</i> Pay
                            </a>
                            <a data-name="invoice" href="#" class="small link-dark d-none align-items-center gap-2" target="_blank">
                                <i class="material-icons icon-sm">receipt</i> Invoice
                            </a>
                            <a
                                data-name="payload"
                                href="#"
                                class="small link-dark d-flex align-items-center gap-2"
                                data-bs-toggle="modal"
                                data-bs-target="#payload-modal">
                                <i class="material-icons icon-sm">text_snippet</i> Payload
                            </a>
                        </div>
                    </div>
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

    <p class="text-muted my-5 text-center mx-auto mw-400px">Bank check may take 10-20 minutes. If you have paid and waited longer, please contact us at email <a class="link-dark" href="mailto:{{ config('etsetra.email') }}">{{ config('etsetra.email') }}</a></p>
@endsection
