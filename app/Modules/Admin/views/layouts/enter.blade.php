{{--

Основной страницы входа в админку

--}}
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="{{ asset(config('add.img') . '/omegakontur/admin/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset(config('add.img') . '/omegakontur/admin/touch-icon-iphone.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/omegakontur/admin/touch-icon-ipad.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset(config('add.img') . '/omegakontur/admin/touch-icon-iphone-retina.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset(config('add.img') . '/omegakontur/admin/touch-icon-ipad-retina.png') }}">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.13/css/all.css">
    {{--<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Material+Icons">--}}
    <link rel="stylesheet" href="{{ asset('css/append.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! $getMeta !!}
    <noscript>
        <div class="container mt-4 mb-2">
            <div class="row">
                <div class="col">
                    <div class="alert alert-danger p-3">@lang("{$lang}::s.Please_enable_JavaScript")</div>
                </div>
            </div>
        </div>
    </noscript>
    @if (config('add.recaptcha_secret_key'))
        <script src="//www.google.com/recaptcha/api.js"></script>
    @endif
</head>
<body class="bg-light">
<div class="d-block app" id="app">
    <div class="container-fluid">
        <div class="mt-2 a-primary">
            <a href="{{ route('index') }}" title="@lang("{$lang}::s.home")">
                <i class="fas fa-th"></i>
            </a>
        </div>
    </div>
    <div class="container">
        @include("{$viewPath}.inc.message")
    </div>
    <div class="content" id="content">
        @yield('content')
    </div>
    <div id="bottom-block"></div>
</div>
<div id="spinner">
    <div class="spinner-block">
        <div class="spinner-border" role="status">
            <span class="sr-only">Загрузка...</span>
        </div>
    </div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@include('views.inc.recaptcha')
<script>
    var height = '{{ config('add.height', 600) }}',
        main = {
            url: '{{ config('add.url', '/') }}'
        },
        table = null

    {!! \App\Helpers\Locale::translationsJson() !!}
</script>
<script src="{{ asset('js/append.js') }}"></script>
</body>
</html>
