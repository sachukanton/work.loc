@php
    global $wrap;
    $_device_type = $wrap['device']['type'] ?? 'pc';
@endphp

@extends('frontend.default.index')
@section('content')
<section class="set_open">
    <div class="container">
        <a href="{{ url()->previous() }}" class="goback">
            <svg>
                <use xlink:href="#left"></use>
            </svg>
            <h6>{{variable('back')}}</h6>
        </a>
        <div class="wrapper">
                <div class="set_open--img_wrapper">
                    @if($_mark_param = $_item->_param_items)
                        <div class="marks">
                            @foreach($_mark_param as $_param_item)
                                @if($_param_item->param_id == 1)
                                    @php
                                        $_icon_mark = $_param_item->icon_fid ? f_get($_param_item->icon_fid) : NULL;
                                    @endphp
                                        {!! image_render($_icon_mark) !!}
                                @endif
                            @endforeach
                        </div>
                    @endif
                    @if($_item->slideShow && count($_item->slideShow['slide']) > 1)
                    @if($_device_type == 'pc')
                        <div class="swiper mySwiper2">
                    @else
                        <div class="swiper mySwiper3">
                    @endif
                        <div class="swiper-wrapper">
                            @foreach($_item->slideShow['slide'] as $_slide)
                            <div class="swiper-slide">
                                {!! $_slide !!}
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @if($_device_type == 'pc')
                    <div thumbsSlider="" class="swiper mySwiper">
                        <div class="swiper-wrapper">
                            @foreach($_item->slideShow['slide'] as $_slide)
                            <div class="swiper-slide">
                                {!! $_slide !!}
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @else
                        {!! image_render($_item->_preview, 'slideShow_600_400', ['attributes' => ['title' => $_item->title, 'alt' => $_item->title, 'uk-img' => TRUE]]) !!}
                    @endif

<!--                     @if($_item->slideShow && count($_item->slideShow['slide']) > 1)
                        <div class="set_open--img active">
                            {!! image_render($_item->_preview, 'slideShow_600_400', ['attributes' => ['title' => $_item->title, 'alt' => $_item->title, 'uk-img' => TRUE]]) !!}
                        </div>
                        @foreach($_item->slideShow['slide'] as $_slide)
                            <div class="set_open--img">
                                {!! $_slide !!}
                            </div>
                        @endforeach
                    @else
                        {!! image_render($_item->_preview, 'slideShow_600_400', ['attributes' => ['title' => $_item->title, 'alt' => $_item->title, 'uk-img' => TRUE]]) !!}
                    @endif -->
                </div>
                <div class="set_open--info_wrapper">
                    <h4>
                        {!! $_wrap['page']['title'] !!}
                    </h4>
                    @if($_item->sub_title)
                        <div class="uk-h4 uk-margin-remove-top uk-text-muted">
                            {!! $_item->sub_title !!}
                        </div>
                    @endif
                    {{--@if(($_param = ($_item->paramOptions[5] ?? NULL)))--}}
                        {{--<div class="uk-marks-hit">--}}
                            {{--@foreach($_param['options'] as $_option_id => $_option_item)--}}
                                {{--{!! $_option_item !!}--}}
                            {{--@endforeach--}}
                        {{--</div>--}}
                    {{--@endif--}}
                    <div class="wrapper" id="shop-product-action-box">
                        <div class="set_open--info-size">
                            @if(($_param = ($_item->paramOptions[3] ?? NULL)))
                                <div id="shop-product-weight-box">
                                    {{--{{ $_param['title'] }}--}}
                                    {{ $_param['options'] . ($_param['unit'] ? "{$_param['unit']}" : NULL) }}
                                </div>
                            @endif
                            @if(($_param = ($_item->paramOptions[4] ?? NULL)))
                                <div>
                                    @if($_item->paramOptions[3] ?? NULL)&nbsp;/&nbsp;@endif
                                    {{ $_param['options'] . ($_param['unit'] ? " {$_param['unit']}" : NULL) }}
                                </div>
                            @endif
                            @if(($_param = ($_item->paramOptions[7] ?? NULL)))
                               <div id="shop-product-weight-box">
                                    {{ $_param['options'] . ($_param['unit'] ? "{$_param['unit']}" : NULL) }}
                                </div>
                            @endif
                        </div>
                        <div class="set_open--info-comp">
                            {{variable('composition')}}
                            @if(($_ingredients_param = ($_item->paramOptions[2] ?? NULL)))
                                <div class="param-option-product">
                                    <div class="uk-text-lowercase">
                                        {{ implode(' | ', $_ingredients_param['options']) }}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="price">
                            @if($_item->price['view_price'])
                                @if(count($_item->price['view']) > 1)
                                    <span class="old_price">
                                        {!! $_item->price['view'][0]['format']['view_price'] !!}
                                    </span>
                                    <span class="real-old price-format">
                                        {!! $_item->price['view'][1]['format']['view_price_2'] !!}
                                    </span>
                                @else
                                    <span class="real-old price-format uk-margin-remove">
                                        {!! $_item->price['view'][0]['format']['view_price_2'] !!}
                                    </span>
                                @endif
                                <div class="product-additional-default"></div>
                            @endif  
                        </div>
                            @include('frontend.default.shops.product_consist')
                            
                        <div class="input uk-input-number-counter-box">
                            <input class="sum" type="number"
                                    value="1"
                                    data-default="1"
                                    min="1"
                                    max="10000000"
                                    step="1"
                                    name="count"
                                    class="uk-input uk-text-center uk-disabled"

                                    autocapitalize="off">
                            <div class="range">
                                <button type="button"
                                        name="increment"
                                        class="plus">
                                    +
                                </button>
                                <button type="button"
                                        class="minus"
                                        name="decrement"
                                        disabled>
                                    -
                                </button>
                            </div>
                        </div>
                        @if(($_param = ($_item->paramOptions[8] ?? NULL)))
                        <div class="set-kit">
                            <div class="set-kit_top">
                                <p>{{variable('set')}}</p>
                                <div class="set-kit--wrapper">
                                    @php
                                        $i = 1;
                                        while ($i <= $_param['options']):
                                        @endphp
                                        <span>
                                            <svg>
                                                <use xlink:href="#user"></use>
                                            </svg>
                                        </span>
                                    @php
                                        $i++;
                                        endwhile;
                                    @endphp
                                </div>
                            </div>
                            <p>{{ $_param['unit'] ? "{$_param['unit']}" : NULL }}</p>
                        </div>
                        @endif
                        <div class="delivery_wrapper">
                            <div class="delivery">
                                <img src="template/images/icons/clock.svg">
                                {!! variable('max_time_3') !!}
                            </div>
                            <div class="btn__wrapper">
                                <button type="button"
                                        data-path="{{ _r('ajax.shop_buy_one_click') }}"
                                        data-product="{{ $_item->id }}"
                                        class="btn--white use-ajax">
                                    {{variable('one_click')}}
                                </button>
                                <button id="shop-product-buy-button" type="button"
                                    data-path="{{ _r('ajax.shop_action_basket', ['shop_price' => $_item->price['id']]) }}"
                                    class="btn">
                                    <svg>
                                        <use xlink:href="#bike"></use>
                                    </svg>
                                    {{variable('cart')}}
                                </button>
                            </div>
                        </div>
                        {{--@if(!is_null($_item->is_spicy))--}}
                            {{--<div>--}}
                                {{--<button type="button"--}}
                                        {{--class="uk-button uk-button-default uk-button-small uk-border-rounded product-spicy-button"--}}
                                        {{--data-button="{{ json_encode(['not_spicy' => trans('shop.marks.spicy_0'), 'is_spicy' => trans('shop.marks.spicy_1')]) }}"--}}
                                        {{--data-spicy="{{ (int) $_item->is_spicy }}"--}}
                                        {{--data-product="{{ $_item->id }}">--}}
                                    {{--@lang('shop.marks.spicy_' . (int) $_item->is_spicy)--}}
                                {{--</button>--}}
                            {{--</div>--}}
                        {{--@endif--}}


                        @if($_item->modification_items && $_item->modification_items->count() > 1)
                            <div class="uk-modification">
                                @foreach($_item->modification_items as $_mod)
                                    <a href="{{ $_mod->generate_url }}"
                                       title="{{ $_mod->title }}"
                                       class="uk-button uk-btn-mod {{ $_mod->id == $_item->id ? 'uk-active uk-disabled' : NULL }}">
                                        {{ $_mod->modify_param_item_title }}
                                    </a>
                                @endforeach
                            </div>
                        @endif



                        {{--@if($_item->productOrder && $_item->productOrder['items']['default'])--}}
                            {{--<div>--}}
                                {{--<div class="uk-card uk-box-shadow-small uk-border-rounded uk-padding-small">--}}
                                    {{--<h3>--}}
                                        {{--Дополнительно к заказу--}}
                                    {{--</h3>--}}
                                    {{--<div class="product-order-default-box">--}}
                                        {{--@foreach($_item->productOrder['items']['default'] as $d)--}}
                                            {{--<div class="uk-margin-small">--}}
                                                {{--<label for="product-order-default-{{ $d['id'] }}">--}}
                                                    {{--<input type="checkbox"--}}
                                                           {{--id="product-order-default-{{ $d['id'] }}"--}}
                                                           {{--value="{{ $d['id'] }}"--}}
                                                           {{--name="product_order_default[]">--}}
                                                    {{--{{ "{$d['title']} {$d['weight']} г {$d['price']} грн" }}--}}
                                                {{--</label>--}}
                                            {{--</div>--}}
                                        {{--@endforeach--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--@endif--}}

                        {{--@if($_item->productOrder['additions'])--}}
                            {{--<div class="uk-card uk-box-shadow-small uk-border-rounded uk-padding-small uk-margin-bottom">--}}
                                {{--@if($_item->productOrder['items']['default'])--}}
                                    {{--<div>--}}
                                        {{--<h3>--}}
                                            {{--Дополнительно к заказу:--}}
                                        {{--</h3>--}}
                                        {{--<div class="product-order-default-box">--}}
                                            {{--@foreach($_item->productOrder['items']['default'] as $d)--}}
                                                {{--<div class="uk-margin-small">--}}
                                                    {{--<label for="product-order-default-double-{{ $d['id'] }}">--}}
                                                        {{--<input type="checkbox"--}}
                                                               {{--id="product-order-default-double-{{ $d['id'] }}"--}}
                                                               {{--value="{{ $d['id'] }}"--}}
                                                               {{--name="product_order_double_default[]">--}}
                                                        {{--{{ "{$d['title']} {$d['weight']} г {$d['price']} грн" }}--}}
                                                    {{--</label>--}}
                                                {{--</div>--}}
                                            {{--@endforeach--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--@endif--}}
                                {{--@if($_item->productOrder['items']['ingredients'])--}}
                                    {{--<div>--}}
                                        {{--<h3>--}}
                                            {{--Ингредиенты:--}}
                                        {{--</h3>--}}
                                        {{--<div class="product-order-ingredients-box">--}}
                                            {{--@foreach($_item->productOrder['items']['ingredients'] as $d)--}}
                                                {{--<div id="product-order-ingredient-{{ $d['id'] }}-row"--}}
                                                     {{--class="uk-grid uk-grid-small uk-margin-small uk-flex uk-flex-middle">--}}
                                                    {{--<div class="uk-width-expand">--}}
                                                        {{--<label for="product-order-ingredient-{{ $d['id'] }}">--}}
                                                            {{--<input type="checkbox"--}}
                                                                   {{--id="product-order-ingredient-{{ $d['id'] }}"--}}
                                                                   {{--value="{{ $d['id'] }}"--}}
                                                                   {{--checked--}}
                                                                   {{--name="product_order_ingredient[]">--}}
                                                            {{--{{ $d['title'] }}--}}
                                                        {{--</label>--}}
                                                    {{--</div>--}}
                                    {{--<div class="product-order-weight"--}}
                                    {{--style="width: 50px">--}}
                                    {{--{{ $d['weight'] }}--}}
                                    {{--</div>--}}
                                    {{--<div class="product-order-quantity uk-flex uk-flex-middle"--}}
                                    {{--style="width: 80px">--}}
                                    {{--<button type="button"--}}
                                    {{--data-ingredient="{{ $d['id'] }}"--}}
                                    {{--name="decrement">---}}
                                    {{--</button>--}}
                                    {{--<div class="input-quantity">--}}
                                    {{--1--}}
                                    {{--</div>--}}
                                    {{--<button type="button"--}}
                                    {{--data-ingredient="{{ $d['id'] }}"--}}
                                    {{--name="increment">+--}}
                                    {{--</button>--}}
                                    {{--</div>--}}
                                                {{--</div>--}}
                                            {{--@endforeach--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--@endif--}}
                                {{--@if($_item->productOrder['items']['additions'])--}}
                                    {{--<div>--}}
                                        {{--<h3>--}}
                                            {{--Дополнительные ингредиенты:--}}
                                        {{--</h3>--}}
                                        {{--<div class="product-order-additions-box">--}}
                                            {{--@foreach($_item->productOrder['items']['additions'] as $d)--}}
                                                {{--<div id="product-order-ingredient-{{ $d['id'] }}-row"--}}
                                                     {{--class="uk-grid uk-grid-small uk-margin-small uk-flex uk-flex-middle not-chosen">--}}
                                                    {{--<div class="uk-width-expand">--}}
                                                        {{--<label for="product-order-addition-{{ $d['id'] }}">--}}
                                                            {{--<input type="checkbox"--}}
                                                                   {{--id="product-order-addition-{{ $d['id'] }}"--}}
                                                                   {{--value="{{ $d['id'] }}"--}}
                                                                   {{--name="product_order_addition[]">--}}
                                                            {{--{{ $d['title'] }}--}}
                                                        {{--</label>--}}
                                                    {{--</div>--}}
                                                    {{--<div class="product-order-weight"--}}
                                                         {{--style="width: 50px">--}}
                                                        {{--{{ $d['weight'] }}--}}
                                                    {{--</div>--}}
                                                    {{--<div class="product-order-quantity uk-flex uk-flex-middle"--}}
                                                         {{--style="width: 80px">--}}
                                                        {{--<button type="button"--}}
                                                                {{--name="decrement"--}}
                                                                {{--data-ingredient="{{ $d['id'] }}"--}}
                                                                {{--disabled="disabled">---}}
                                                        {{--</button>--}}
                                                        {{--<div class="input-quantity">--}}
                                                            {{--0--}}
                                                        {{--</div>--}}
                                                        {{--<button type="button"--}}
                                                                {{--data-ingredient="{{ $d['id'] }}"--}}
                                                                {{--name="increment">+--}}
                                                        {{--</button>--}}
                                                    {{--</div>--}}
                                                {{--</div>--}}
                                            {{--@endforeach--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--@endif--}}
                                {{--<div class="uk-flex uk-flex-middle">--}}
                                    {{--<div class="uk-width-expand">--}}
                                        {{--Итого:--}}
                                        {{--<div id="shop-product-total-box"--}}
                                             {{--class="product-order-total uk-display-inline-block">--}}
                                            {{--{!! str_replace(':price', $_item->productOrder['product']['price']['value'], $_item->productOrder['product']['price']['label']) !!}--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="uk-width-auto">--}}
                                        {{--<button type="button"--}}
                                                {{--class="uk-button uk-button-primary uk-border-rounded">--}}
                                            {{--Готово--}}
                                        {{--</button>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--@endif--}}

                    </div>
                </div>
        </div>
        @if($_item->teaser || $_item->specification)
            <div class="teaser-product">
                <div class="uk-container uk-container-expand">
                    @if($_item->teaser)
                        <div class="param-title uk-text-uppercase">
                            @lang('frontend.titles.teaser_product')
                        </div>
                    @endif
                    <div class="uk-grid">
                        @if($_item->teaser)
                            <div class="uk-width-expand@m">
                                <div class="teaser">
                                    {!! $_item->teaser !!}
                                </div>
                            </div>
                        @endif
                        @if($_item->specification)
                            <div class="uk-width-large@xl uk-width-medium@m specification-product">
                                @foreach($_item->specification as $_specification)
                                    <div class="name">
                                        {!! $_specification[0] !!}
                                    </div>
                                    <div class="text">
                                        {!! $_specification[1] !!}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
<!--     <div class="card-descriptions uk-margin-medium-top">
        <div class="uk-container">
            @if($_item->body)
                <div class="">
                    {!! $_item->body !!}
                </div>
            @endif
            @if($_item->relatedFiles && $_item->relatedFiles->isNotEmpty())
                <div class="entity-files">
                    @include('frontend.default.partials.entity_files')
                </div>
            @endif
        </div>
    </div> -->
</div>
</section>
    @include('frontend.default.shops.product_related')

    @if($_item->body)
        <section class="seo__text">
            <div class="container">
                {!! $_item->body !!}
            </div>
        </section>
    @endif
<script type="text/javascript">
    window.product_info = {!! json_encode($_item->productOrder['product']) !!};
    window.additionally_ingredients = {!! json_encode($_item->productOrder['items']) !!};
</script>
@endsection

@push('edit_page')
    @if(isset($_accessEdit['shop_product']) && $_accessEdit['shop_product'])
        <div class="uk-position-fixed uk-position-top-right uk-margin-small-top uk-margin-small-right">
            <button class="uk-button uk-button-color-amber"
                    type="button">
                <span uk-icon="icon: settings"></span>
            </button>
            <div uk-dropdown="pos: bottom-right; mode: click"
                 class="uk-box-shadow-small uk-padding-small">
                <ul class="uk-nav uk-dropdown-nav">
                    <li>
                        @if($_locale == DEFAULT_LOCALE)
                            @l('<span uk-icon="icon: createmode_editedit; ratio: .7"
                                      class="uk-margin-small-right"></span>редактировать', 'oleus.shop_products.edit',
                            ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' =>
                            'uk-link-primary']])
                        @else
                            @l('<span uk-icon="icon: createmode_editedit; ratio: .7"
                                      class="uk-margin-small-right"></span>редактировать',
                            'oleus.shop_products.translate', ['p' => ['shop_product' => $_item->id, 'locale' =>
                            $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-link-primary']])
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    @endif
@endpush
@push('scripts')

<script src="/template/js/jquery.inputmask.bundle.min.js"
        type="text/javascript"></script>

    <script type="text/javascript">
        if (typeof fbq == '') {
            var a = {};
            if (typeof FbData == 'object') attr = Object.assign(a, FbData);
            a.content_type = 'product';
            a.content_category = {!! $_item->cat !!};
            a.content_ids = '{{ $_item->sku }}';
            a.value = {{ $_item->price['view_price'] ? $_item->price['view'][0]['format']['price'] : NULL }};
            a.currency = 'UAH';
            fbq('track', 'ViewContent', a);
        }
        @if($_item->_eCommerce->isNotEmpty())
        if (typeof gtag == "function") {
            gtag("event", "view_item", {items: {!! $_item->_eCommerce->toJson() !!} });
        }
        @endif
    </script>
@endpush
