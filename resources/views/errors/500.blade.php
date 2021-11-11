@extends('errors.minimal')

@section('content')
    <div class="error-404 uk-flex uk-flex-bottom" style="background-image: url('template/images/bg-500.png')">
    <div class="uk-container uk-container-small">
        <div class="box-error error-server uk-text-center">
            {{--<div class="box-error-title">--}}
                {{--<h1 class="title-01 uk-position-relative uk-display-inline-block uk-text-uppercase">--}}
                    {{--500--}}
                    {{--@lang('frontend.titles.error')--}}
                {{--</h1>--}}
            {{--</div>--}}
            <div class="title uk-margin-remove">
                @lang('frontend.page_server_error')
            </div>
            <div class="box-btn-link uk-margin-bottom">
                <a href="{{ _u(LaravelLocalization::getLocalizedURL($_wrap['locale'], '/')) }}"
                   class="uk-button uk-button-link">
                    @lang('frontend.go_home')
                </a>
            </div>
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



