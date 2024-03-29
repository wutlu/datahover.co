<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ $title = @$title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@300;400;700&family=Material+Icons&display=swap" />
    <link rel="stylesheet" href="{{ asset('css/app.min.css') }}" />

    <link rel="canonical" href="{{ url()->current() }}" />
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <meta name="robots" content="all" />
@section('hide')
    <meta name="author" content="{{ config('etsetra.company_name') }}" />
@endsection
    <meta name="publisher" content="{{ config('app.name').' - '.config('app.version') }}" /> 

    @isset($description)
        <meta name="description" content="{{ $description }}" />
    @endisset

    {{-- Open Graphs --}}
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $title }}"/>
    @isset($description)<meta property="og:description" content="{{ $description }}" />@endisset
    <meta property="og:image" content="{{ asset('images/home-preview.jpg') }}" />

@section('hide')
    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="{{ '@'.config('etsetra.social.twitter.screen_name') }}" />
    <meta name="twitter:title" content="{{ $title }}" />
    @isset($description)<meta name="twitter:description" content="{{ $description }}" />@endisset
    <meta name="twitter:image" content="{{ asset('images/home-preview.jpg') }}" />
@endsection

    @stack('head')

    <style>
        @stack('css')
    </style>
</head>
<body class="preloader-active">
    @isset($master)
        @include('includes.navbar')

        @isset($breadcrumb)
            <nav class="shadow-sm bg-dark-2">
                <div class="container">
                    <ul class="list-unstyled m-0 py-1 d-flex flex-wrap align-items-start align-items-sm-center gap-0 gap-sm-3 flex-column flex-sm-row">
                        @foreach ($breadcrumb as $name => $route)
                        <li>
                            @if (is_array($route))
                                <span class="btn-group">
                                    <a title="{{ $name }}" href="#" class="dropdown-toggle small link-dark fw-bold text-white" data-bs-toggle="dropdown">{{ $name }}</a>
                                    <ul class="dropdown-menu shadow dropdown-menu-end border-0">
                                        @foreach ($route as $n => $r)
                                            <li><a title="{{ $n }}" href="{{ $r }}" class="dropdown-item">{{ $n }}</a></li>
                                        @endforeach
                                    </ul>
                                </span>
                            @else
                                @if ($route == '#')
                                    <span class="small link-dark fw-bold text-white">{{ $name }}</span>
                                @else
                                    <a title="{{ $name }}" class="small link-secondary" href="{{ $route }}">{{ $name }}</a>
                                @endif
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
            </nav>
        @endisset

        <div class="container">
            <main class="py-3">
                <div class="row">
                    <div class="col-12 col-md-8 col-lg-9">
                        @yield('content')
                    </div>
                    <div class="col-12 col-md-4 col-lg-3">
                        @include('includes.drawer')
                    </div>
                </div>
            </main>
        </div>
    @else
        @yield('content')
    @endisset

    @include('includes.modals.alert')
    @include('includes.modals.cookie')
    @include('includes.modals.error')

    <!-- Begin: Toast -->
    <div class="toast-wrapper w-100 position-fixed right-0 bottom-0 left-0 d-flex flex-column align-items-center"></div>
    <!-- End: Toast -->

    <script>
        let env = '{{ config('app.env') }}';
        let keywords = {
            'ok': 'Ok',
            'cancel': 'Cancel',
            'copied': 'Copied',
        };
        let messages = {
            0: 'Internet connection problem!',
            401: 'You are not authorized for this page.',
            403: 'test',
            404: '404 not found',
            405: 'test',
            422: 'test',
            429: 'You made too many requests. Please calm down and try again later.',
            500: 'test',
            'unknown': 'Unknown Error',
            'required_form_id': 'Dear developer, please enter an ID for the form.',
        };
        let routes = {
            'base': '{{ config('app.url') }}',
            'index': '{{ route('index') }}',
            'info': '{{ route('user.info') }}',
        };
    </script>
    <script src="{{ asset('js/app.min.js') }}"></script>
    <script>
        @stack('js')
    </script>

    @stack('footer')

    @if ($code = config('services.jivo.code'))
        <script src="//code-eu1.jivosite.com/widget/{{ $code }}" async></script>
    @endif

    @if ($project_key = config('services.smartlook.project_key'))
        <script>
            window.smartlook||(function(d) {
                var o = smartlook = function() {
                    o.api.push(arguments)},
                    h = d.getElementsByTagName('head')[0];
                var c = d.createElement('script');
                    o.api = new Array();
                    c.async = true;
                    c.type = 'text/javascript';
                    c.charset = 'utf-8';
                    c.src = 'https://rec.smartlook.com/recorder.js';
                    h.appendChild(c);
            })(document);

            smartlook('init', '{{ $project_key }}');
        </script>
    @endif

    @if ($code = config('services.google_analytics.code'))
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $code }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag()
            {
                dataLayer.push(arguments)
            }

            gtag('js', new Date());

            gtag('config', '{{ $code }}');
        </script>
    @endif
</body>
</html>
