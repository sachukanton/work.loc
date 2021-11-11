@php
    global $wrap;
    $_device_type = $_wrap['device']['type'];
@endphp

@extends('frontend.default.index')

@section('content')
        {{--@include('backend.base.breadcumb', ['_items' => $_wrap['page']['breadcrumb']])--}}

        @sliderRender('1')
        @menuRender('2')
        @advantageRender('1')

        @if(isset($_others['recommended_front']) && $_others['recommended_front'])
            {!! $_others['recommended_front']['object'] !!}
        @endif
        @if(isset($_others['new']) && $_others['new'])
            {!! $_others['new']['object'] !!}
        @endif


<!--         @menuRender('3')
        {!! App\Models\Structure\Node::getNodeSlider() !!} -->

        {{--@if($_device_type == 'pc')--}}
        {{--@menuRender('2')--}}
        {{--@endif--}}
        {{--@if(isset($_others['recommended_front']) && $_others['recommended_front'])--}}
            {{--{!! $_others['recommended_front']['object'] !!}--}}
        {{--@endif--}}
        {{-- <article class="uk-article uk-position-relative"> --}}

            {{--<load-component--}}
                    {{--entity="shop_product_view_list_new"--}}
                    {{--options=""></load-component>--}}

            {{--<load-component--}}
            {{--entity="page_last_nodes"--}}
            {{--options="id=13;count_items=3"></load-component>--}}

            {{--<load-component--}}
                    {{--entity="shop_product_view_list_hit"--}}
                    {{--options=""></load-component>--}}
            {{--<load-component--}}
                    {{--entity="shop_product_view_list_discount"--}}
                    {{--options=""></load-component>--}}
            {{--<load-component--}}
                    {{--entity="shop_product_view_list_recommended_front"--}}
                    {{--options=""></load-component>--}}

        {{-- </article> --}}
        <section class="seo__text" style="background-image: url(/template/images/bg-bottom.png); background-position: top; background-size: contain;">
            <div class="container">
                    <h2>
                       {!! $_wrap['page']['title'] !!}
                    </h2>
                    @if($_item->sub_title)
                        <h3>
                            {!! $_item->sub_title !!}
                        </h3>
                    @endif
                    @if($_item->body)
                        {!! $_item->body !!}
                    @endif
                </div>
            </div>
        </section>
@endsection

{{--@push('edit_page')--}}
    {{--@if(isset($_accessEdit['page']) && $_accessEdit['page'])--}}
        {{--<div class="uk-position-fixed uk-position-top-right uk-margin-small-top uk-margin-small-right">--}}
            {{--<button class="uk-button uk-button-color-amber"--}}
                    {{--type="button">--}}
                {{--<span uk-icon="icon: settings"></span>--}}
            {{--</button>--}}
            {{--<div uk-dropdown="pos: bottom-right; mode: click"--}}
                 {{--class="uk-box-shadow-small uk-padding-small">--}}
                {{--<ul class="uk-nav uk-dropdown-nav">--}}
                    {{--<li>--}}
                        {{--@if($_locale == DEFAULT_LOCALE)--}}
                            {{--@l('<span uk-icon="icon: createmode_editedit; ratio: .7" class="uk-margin-small-right"></span>редактировать', 'oleus.pages.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])--}}
                        {{--@else--}}
                            {{--@l('<span uk-icon="icon: createmode_editedit; ratio: .7" class="uk-margin-small-right"></span>редактировать', 'oleus.pages.translate', ['p' => ['page' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])--}}
                        {{--@endif--}}
                    {{--</li>--}}
                {{--</ul>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--@endif--}}
{{--@endpush--}}
