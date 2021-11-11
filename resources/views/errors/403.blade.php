@extends('errors.minimal')

@section('content')
    <div class="uk-container uk-container-expand">
        <div id="breadcrumbs">
            <ul class="uk-breadcrumb">
                <li>
                    <a href="{{ _u(LaravelLocalization::getLocalizedURL($_wrap['locale'], '/')) }}">
                        @lang('pages.titles.home')
                    </a>
                </li>
                <li>
                    <span>
                        403
                   </span>
                </li>
            </ul>
        </div>
        <div class="box-error uk-text-center active">
            <div class="title-error uk-text-uppercase">
                ошибка 403:
            </div>
            <div class="sub-title-error uk-text-uppercase">
                отказано в доступе :(
            </div>
            <div>
                <a href="{{ _u(LaravelLocalization::getLocalizedURL($_wrap['locale'], '/')) }}"
                   class="uk-button uk-button-success uk-text-uppercase">
                    на главную
                </a>
            </div>
            <div class="time-out-thanks uk-text-uppercase">
                переход на главную страницу через <span><i class="min"></i> сек.</span>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // window.setTimeout(function () {
        //     window.location.href = '/';
        // }, 5000);
    </script>
@endpush

