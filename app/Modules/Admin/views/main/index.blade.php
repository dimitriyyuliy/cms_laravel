@extends('layouts.admin')

@section('content')
    <section>
        <div class="row">
            @if (\App\App::issetModule('Shop'))
                {!! $constructor::adminMainBlock('Orders', $count_orders ?? '0', 'shopping_cart', 'order') !!}
            @endif
            {!! $constructor::adminMainBlock('Forms', $count_forms ?? '0', 'insert_comment', 'form') !!}
            {!! $constructor::adminMainBlock('Pages', $count_pages ?? '0', 'web', 'page') !!}
            {!! $constructor::adminMainBlock('Users', $count_users ?? '0', 'supervisor_account', 'user') !!}
        </div>
    </section>

    <section>
        <div class="row mt-4">
            <div class="col">
                <h5 class="text-primary">{{ auth()->user()->name . __('c.welcome') }}</h5>
            </div>
        </div>
    </section>

    <section class="transliterator">
        <div class="row mt-5 mb-3">
            <div class="col">
                {!! $constructor::adminH2(__('a.transliterator')) !!}
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="d-flex justify-content-between w-100">
                    <div class="w-96">
                        {!! input('cyrillic') !!}
                    </div>
                    <div class="mt-4">
                        <button class="btn btn-outline-primary btn-sm d-flex align-items-center mt-1 btn-pulse p-icons material-icons" id="transliterator" title="{{ __('a.generate') }}">autorenew</button>
                    </div>
                </div>
                {!! input('latin') !!}
                {{--{!! input('latin', null, null, 'text', null, null, null, ['disabled' => null]) !!}--}}
            </div>
        </div>
    </section>
    {{--

    Если не включена авторизация на сайте, то можно сформировать slug для входа --}}
    @if (!\App\App::issetModule('Auth'))
        <section class="key-to-enter">
            <div class="row mt-5 mb-3">
                <div class="col">
                    {!! $constructor::adminH2(__('a.key_to_enter')) !!}
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="d-flex justify-content-between w-100">
                        <div class="w-96">
                            {!! input('to_change_key', $key->key) !!}
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-outline-primary btn-sm d-flex align-items-center mt-1 btn-pulse p-icons material-icons" id="key-to-enter" title="{{ __('f.save') }}">save_alt</button>
                        </div>
                    </div>
                    <p class="text-secondary"><sup>*</sup> {{ __('a.key_description') }}</p>
                </div>
            </div>
            @if (\App\App::get('settings')['change_key'] ?? null)
                <div class="row">
                    <div class="col-md-6">
                        {!! input('created_at', d($key->date_key, config('admin.date_format')), null, 'text', true, null, null, ['disabled' => null]) !!}
                    </div>
                    <div class="col-md-6">
                        {!! input('will_be_updated', d($key->date_upload, config('admin.date_format')), null, 'text', true, null, null, ['disabled' => null])!!}
                    </div>
                </div>
            @endif
        </section>
    @endif

    {{--<div class="row mt-5 mb-3">
        <div class="col">
            {!! $constructor::adminH2('Testing informer') !!}
        </div>
    </div>

    <div class="row">
        {!! $constructor::adminGrayBlock('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ex officia rem veniam. Ducimus, repellendus?', 'pages') !!}
    </div>

    <div class="row mt-5">
        <div class="col">
            {!! $constructor::adminH2('Testing informer') !!}
        </div>
    </div>

    <div class="row">
        {!! $constructor::adminInfoBlock('Testing', 22) !!}
    </div>--}}
@endsection
