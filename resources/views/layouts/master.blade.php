<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ @$title ? $title.' - '.config('app.name') : config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+Display:wght@200;400;600&family=Material+Icons&display=swap" />
    <link rel="stylesheet" href="{{ asset('css/app.min.css') }}" />

    <link rel="canonical" href="{{ url()->current() }}" />

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
                            @if ($route == '#')
                                <span class="small link-dark fw-bold text-white">{{ $name }}</span>
                            @else
                                <a class="small link-secondary" href="{{ $route }}">{{ $name }}</a>
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
            404: 'test',
            405: 'test',
            422: 'test',
            429: 'You made too many requests. Please calm down and try again later.',
            500: 'test',
            'unknown': 'Unknown Error',
            'required_form_id': 'Dear developer, please enter an ID for the form.',
        };
        let routes = {
            'base': '{{ config('app.url') }}',
            'index': '{{ route('index') }}'
        };
    </script>
    <script src="{{ asset('js/app.min.js') }}"></script>
    <script>
        @stack('js')
    </script>

    @stack('footer')
</body>
</html>
