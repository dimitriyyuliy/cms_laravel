{{--

Подключаем css файл --}}
@if (File::exists(public_path("css/{$m}.css")))
    @section('css')
        <link rel="stylesheet" type="text/css" href="{{ asset("css/{$m}.css") }}">
    @endsection
@endif
{{--

Наследуем шаблон --}}
@extends("{$viewPath}.default")
{{--

Подключается блок header --}}
@section('header')
    @include("{$viewPath}.inc.header")
@endsection
@section('content')
    <main class="main">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="font-weight-light text-secondary mt-5">{{ $title }}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col pt-4 no_js">
                    @include("{$viewPathModule}.cart_modal")
                    @if (!$cartSession)
                        <a href="{{ route('catalog') }}" class="btn btn-primary mt-4">@lang("{$lang}::s.catalog")</a>
                    @endif
                </div>
            </div>
            @if ($cartSession)
                <div class="row">
                    <div class="col-md-6 mt-3 mb-5">
                        <form method="post" action="{{ route('make_order') }}" class="needs-validation spinner_submit" novalidate>
                        @csrf
                        {!! input('name', null, true, null, null) !!}
                        {!! input('tel', null, true, 'tel', null) !!}
                        {!! input('email', null, true, null, null) !!}
                        {!! textarea('address', null, true, null, 'address') !!}
                        {!! textarea('message', null, null, null, 'message') !!}
                        {!! checkbox('accept', null, true) !!}
                        <button type="submit" class="btn btn-primary">@lang("{$lang}::f.submit")</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </main>
@endsection
{{--

Подключается блок footer

--}}
@section('footer')
    @include("{$viewPath}.inc.footer")
@endsection
{{--

Подключаем js файл --}}
@if (File::exists(public_path("js/{$m}.js")))
    @section('js')
        <script src="{{ asset("js/{$m}.js") }}" defer></script>
    @endsection
@endif
