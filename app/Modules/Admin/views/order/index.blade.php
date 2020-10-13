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
                                <span>@lang("{$lang}::f.user_id")</span>
                                {!! $dbSort::viewIcons('user_id', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.name")</span>
                                {!! $dbSort::viewIcons('name', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.email")</span>
                                {!! $dbSort::viewIcons('email', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.tel")</span>
                                {!! $dbSort::viewIcons('tel', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::s.qty")</span>
                                {!! $dbSort::viewIcons('qty', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::s.sum")</span>
                                {!! $dbSort::viewIcons('sum', $view, $route) !!}
                            </th>
                            <th scope="col" class="font-weight-light">
                                <span>@lang("{$lang}::f.status")</span>
                                {!! $dbSort::viewIcons('status', $view, $route) !!}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($values as $v)
                            @php

                            if ($v->status === config('admin.order_statuses')[0]) {
                                $trClass = 'table-danger';

                            } elseif ($v->status === config('admin.order_statuses')[1]) {
                                $trClass = 'table-warning';

                            /*} elseif () {*/

                            } else {

                                $trClass = null;
                            }

                            @endphp
                            <tr class="{{ $trClass }}">
                                <th scope="row" class="d-flex align-items-center">
                                    <a href="{{ route("admin.{$route}.show", $v->id) }}" class="font-weight-light">
                                        <i class="fas fa-eye" title="@lang("{$lang}::a.edit")"></i>
                                    </a>
                                    {{--<form action="{{ route("admin.{$route}.destroy", $v->id) }}" method="post" class="confirm-form">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-pulse"><i aria-hidden="true" class="material-icons" title="@lang("{$lang}::s.remove")">delete_outline</i></button>
                                    </form>--}}
                                </th>
                                <td class="font-weight-light">{{ $v->id }}</td>
                                <td>{{ $v->user->id }}</td>
                                <td>
                                    <a href="{{ route("admin.user.edit", $v->user->id) }}">{{ $v->user->name }}</a>
                                </td>
                                <td class="font-weight-light">{{ $v->user->email }}</td>
                                <td>{{ $v->user->tel }}</td>
                                <td class="font-weight-light">{{ $v->qty }}</td>
                                <td class="font-weight-light">{{ $v->sum }}</td>
                                <td class="font-weight-light">@lang("{$lang}::s.{$v->status}")</td>
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
    @endif
@endsection
