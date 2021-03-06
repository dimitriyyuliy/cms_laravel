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
{{--


Вывод контента

--}}
@section('content')
    @if ($values)
        <main class="main">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <h1 class="font-weight-light text-secondary mt-5">{{ $values->title }}</h1>
                    </div>
                </div>
                @if ($products->isNotEmpty())
                    <div class="row my-3">
                        <div class="col-md-3">
                            {!! Filter::init([
                                'cache' => false,
                            ]) !!}
                        </div>
                        <div class="col-md-9">
                            <div class="row products">
                                @foreach ($products as $product)
                                    <div class="col-md-4 mb-4">
                                        <div class="card">
                                            <a href="{{ route('product', $product->slug) }}">
                                                <img src="{{ asset(webp($product->img)) }}" class="card-img-top" alt="{{ $product->title }}">
                                            </a>
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <a href="{{ route('product', $product->slug) }}">
                                                        {{ $product->title }}
                                                    </a>
                                                </h5>
                                                <p class="card-text">Some quick example text to build on the card title.</p>
                                                <a href="{{ route('cart_plus', $product->id) }}" class="btn btn-outline-dark btn-sm cart_plus" data-id="{{ $product->id }}">@lang("{$lang}::s.add_to_cart")</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                {{--

                                Пагинация--}}
                                <div class="col-12 d-flex justify-content-center mt-4">
                                    <div>{{ $products->links() }}</div>
                                </div>
                                <div class="col-12">
                                    <p class="font-weight-light text-center text-secondary mt-3">{{ __("{$lang}::a.shown") . $products->count() . __("{$lang}::a.of") .  $products->total()}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col my-4">
                        {!! $values->body !!}
                    </div>
                </div>
            </div>
        </main>
    @endif
@endsection
{{--

Подключается блок footer --}}
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
