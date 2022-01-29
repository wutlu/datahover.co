<div class="accordion" id="apiAccordion">
    @foreach ($apis as $key => $api)
        <div class="accordion-item border-0 rounded-0 border-top">
            <div class="accordion-header">
                <button
                    class="accordion-button bg-light rounded-0 shadow-none collapsed py-2"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#{{ $key }}">
                    <small class="text-muted">{{ $api['name'] }}</small>
                </button>
            </div>
            <div id="{{ $key }}" class="accordion-collapse collapse border-0" data-bs-parent="#apiAccordion">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item ps-4 d-flex flex-column border-0">
                        <small class="text-muted">{{ $api['method'] }}</small>
                        <label>{{ $api['route'] }}</label>
                    </li>
                    <li class="list-group-item ps-4 d-flex flex-column border-0">
                        <small class="text-muted">Request Limit</small>
                        <label>{{ $rate_limit }} request per {{ $rate_minutes }} minutes</label>
                    </li>
                    <li class="list-group-item fw-bold border-0 text-uppercase">Headers</li>
                    <li class="list-group-item ps-4 d-flex flex-column border-0 py-1">
                        <small class="text-muted">X-Api-Key</small>
                        <label>{{ auth()->user()->api_key ?? 'user_key' }}</label>
                    </li>
                    <li class="list-group-item ps-4 d-flex flex-column border-0 py-1">
                        <small class="text-muted">X-Api-Secret</small>
                        <label>{{ auth()->user()->api_secret ?? 'user_key' }}</label>
                    </li>
                    <li class="list-group-item ps-4 d-flex flex-column border-0 py-1">
                        <small class="text-muted">Accept</small>
                        <label>application/json</label>
                    </li>

                    <li class="list-group-item fw-bold border-0 text-uppercase">Params</li>

                    @foreach ($api['params'] as $key => $value)
                        <li class="list-group-item ps-4 d-flex flex-column border-0 py-1">
                            <small class="text-muted">{{ $key }}</small>
                            <label>{{ $value }}</label>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endforeach
</div>
