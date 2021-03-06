@extends("{$viewPath}.layouts.admin")
{{--

Вывод контента

--}}
@section('content')
    @if ($values->isNotEmpty())
        <div class="row">
            <div class="col">
                @include("{$viewPath}.inc.search")
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col" class="font-weight-light">@lang("{$lang}::a.action")</th>
                            <th scope="col" class="font-weight-light">
                                <span>ID</span>
                                {!! $dbSort::viewIcons('id', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.title")</span>
                                {!! $dbSort::viewIcons('title', $view, $route) !!}
                            </th>
                            {{--<th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.type")</span>
                                {!! $dbSort::viewIcons('type', $view, $route) !!}
                            </th>--}}
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.value")</span>
                                {!! $dbSort::viewIcons('value', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.section")</span>
                                {!! $dbSort::viewIcons('section', $view, $route) !!}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($values as $v)
                            <tr>
                                <th scope="row">
                                    <a href="{{ route("admin.{$route}.edit", $v->id) }}" class="font-weight-light">
                                        <i class="fas fa-eye" title="@lang("{$lang}::a.edit")"></i>
                                    </a>
                                </th>
                                <td class="font-weight-light">{{ $v->id }}</td>
                                <td>{{ $v->title }}</td>
                                {{--<td>{{ $v->type }}</td>--}}
                                <td class="font-weight-light">{{ Str::limit($v->value, 20) }}</td>
                                <td class="font-weight-light">{{ $v->section }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{--

        Подключаем пагинацию --}}
        @include("{$viewPath}.inc.pagination")
        <div class="row">
            <div class="col">
                <div class="border text-secondary rounded my-4 p-4">
                    <span class="font-weight-light">@lang("{$lang}::a.example_use_in_views")</span>
                    <span>@{{ Main::site('name') }}</span>
                </div>
            </div>
        </div>
    @endif
@endsection
