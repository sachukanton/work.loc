@extends('errors.minimal')

@section('content')
<section class="not_found">
    <div class="container" style="background-image: url(template/images/404-side.png);">
        <div class="not_found__wrapper">
            <img src="template/images/404.png" alt="404">
            <p>{!! variable('not_found') !!}</p>
        </div>
    </div>
</section>
@menuRender('2', ['view'=>'frontend.default.menus.menu_2_404'])
   <!--  <div class="error-404 uk-flex uk-flex-bottom">
    <div class="uk-container uk-container-small ">
        <div class="box-error uk-text-center">
            {{--<div class="box-error-title">--}}
                {{--<h1 class="title-01 uk-position-relative uk-display-inline-block uk-text-uppercase">--}}
                    {{--404--}}
                    {{--@lang('frontend.titles.error')--}}
                {{--</h1>--}}
            {{--</div>--}}
            <div class="title uk-margin-remove">
                @lang('frontend.page_not_found')
            </div>
            <div class="box-btn-link uk-margin-bottom">
                <a href="{{ _u(LaravelLocalization::getLocalizedURL($_wrap['locale'], '/')) }}"
                   class="uk-button uk-button-link">
                    @lang('frontend.go_home')
                </a>
            </div>
        </div>
    </div>
    </div> -->
@endsection

@push('scripts')
<script>
    // window.setTimeout(function () {
    //     window.location.href = '/';
    // }, 5000);
</script>
@endpush

