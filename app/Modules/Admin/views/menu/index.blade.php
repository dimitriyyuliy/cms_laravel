@extends("{$viewPath}.layouts.admin")
{{--

Вывод контента

--}}
@section('content')
    @if ($parentValues)
        <div class="row mb-4">
            <div class="col">
                {!! $constructor::select('current_menu', $parentValues, $currentParentId, true, null, ['data-action' => route("admin.{$route}.index")], null, true, null, 'select-change') !!}
            </div>
        </div>
    @endif
    @if ($values && $values->isNotEmpty())
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
                                <span>@lang("{$lang}::a.title")</span>
                                {!! $dbSort::viewIcons('title', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::a.slug")</span>
                                {!! $dbSort::viewIcons('slug', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.status")</span>
                                {!! $dbSort::viewIcons('status', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.sort")</span>
                                {!! $dbSort::viewIcons('sort', $view, $route) !!}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($values as $v)
                            <tr @if ($v->status === config('add.page_statuses')[0]) class="table-active"@endif>
                                <th scope="row">
                                    <a href="{{ route("admin.{$route}.edit", $v->id) }}" class="font-weight-light">
                                        <i class="fas fa-eye" title="@lang("{$lang}::a.edit")"></i>
                                    </a>
                                </th>
                                <td class="font-weight-light">{{ $v->id }}</td>
                                <td>{{ Lang::has("{$lang}::t.{$v->title}") ? __("{$lang}::t.{$v->title}") : $v->title }}</td>
                                <td class="font-weight-light">{{ $v->slug }}</td>
                                <td class="font-weight-light">@lang("{$lang}::s.{$v->status}")</td>
                                <td class="font-weight-light">{{ $v->sort }}</td>
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
                    <div class="font-weight-light">@lang("{$lang}::a.example_use_in_views")</div>
                    <div>@{!! Menu::init([ // @lang("{$lang}::a.described_in_detail") /app/Widgets/Menu/Menu.php</div>
                    <div class="ml-4">'tpl' => 'default',</div>
                    <div class="ml-4">'cache' => true,</div>
                    <div class="ml-4">'cacheName' => 'top_menu',</div>
                    <div class="ml-4">'table' => 'menu',</div>
                    <div class="ml-4">'where' => [['belong_id', 1], ['status', $statusActive]],</div>
                    <div class="ml-4">'container' => 'ul',</div>
                    <div class="ml-4">'class' => 'navbar-nav mr-auto',</div>
                    <div class="ml-4">'classLi' => 'nav-item',</div>
                    <div class="ml-4">'classLink' => 'nav-link',</div>
                    <div>]) !!}</div>
                </div>
            </div>
        </div>
    @endif
@endsection
