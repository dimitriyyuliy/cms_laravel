{{--

Наследуем шаблон --}}
@extends("{$viewPath}.default")
{{--

Подключается блок header --}}
@section('header')
    @include("{$viewPath}.inc.header")
@endsection
{{--


Вывод контента

--}}
@section('content')
    <main class="main">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="font-weight-light text-secondary mt-5">{{ $title }}</h1>
                </div>
            </div>

            <div class="row">
                <div class="col my-4">
                    <form class="form-inline my-2 my-lg-0 search_js" autocomplete="off">
                        <input type="text" class="form-control mr-sm-2 search_js__input" name="s" placeholder="@lang("{$lang}::a.search")" aria-label="Search" value="{{ $searchQuery }}">
                        <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">@lang("{$lang}::a.search")</button>
                        <div class="search_js__child"></div>
                    </form>
                </div>
            </div>

            @if ($values)
                <div class="row mt-4">
                    <div class="col-12">
                        @foreach ($values as $key => $value)
                            <a href="{{ route($value->route, $value->slug) }}" class="d-block my-2">{{ $value->title }}</a>
                        @endforeach
                    </div>
                    <div class="col-12 d-flex justify-content-center">
                        <div>{{ $values->appends(['col' => s(request()->query('s'))])->links() }}</div>
                    </div>
                    <div class="col-12">
                        <p class="font-weight-light text-center text-secondary mt-3">{{ __("{$lang}::a.shown") . $values->count() . __("{$lang}::a.of") .  $values->total()}}</p>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col my-4">
                        <h5>@lang("{$lang}::s.nothing_found")</h5>
                    </div>
                </div>
            @endif
        </div>
    </main>
@endsection
{{--

Подключается блок footer --}}
@section('footer')
    @include("{$viewPath}.inc.footer")
@endsection
