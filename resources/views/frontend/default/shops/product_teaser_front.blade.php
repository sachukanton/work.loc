@php
    $_mark = $_item->mark[0] ?? NULL;
    $_double_card = $_item->double_card == 1 ? 'uk-width-1-2' : 'uk-width-1-4@l uk-width-1-3@m uk-width-1-2';
    $_pizza_card = NULL;
    if($_categories = $_item->_category) foreach($_categories as $_category) if($_category->id == 12) $_pizza_card = 'pizza';
    $_device_type = wrap()->get('device.type');
@endphp

<li class="{{ $_double_card }} {{ $_pizza_card }}">
    <div class="uk-height-1-1 item-card-product">
        <div class="uk-position-relative uk-overflow-hidden uk-tile-product product-id-{{ $_item->id }} @if($_item->price['count_in_basket'] >= 1) add-basket @endif uk-flex uk-flex-column {{ isset($_class) ? " {$_class} " : NULL }}">
            {{--<div class="product-color">--}}
            {{--<div class="item-color"></div>--}}
            {{--</div>--}}
            <div class="uk-tile-top uk-position-relative uk-overflow-hidden uk-position-z-index">
                <div class="preview-product uk-margin-auto uk-flex uk-flex-center uk-flex-middle">
                    <a href="{{ $_item->generate_url }}"
                       rel="nofollow">
                            @if($_item->preview_fid)
                                @if($_item->double_card == 1)
                                    {!! $_item->_preview_asset('productTeaser_640_340', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-img' => true]]) !!}
                                @else
                                    @if($_device_type == 'mobile')
                                        @if($_item->mobile_fid)
                                            {!! image_render($_item->_preview_mobile, 'productTeaser_169_150', ['attributes' => ['title' => $_item->title, 'alt' => $_item->title, 'uk-img' => true]]) !!}
                                        @else
                                            {!! $_item->_preview_asset('productTeaser_169_150', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-img' => true]]) !!}
                                        @endif
                                    @else
                                        {!! $_item->_preview_asset('productTeaser_384_340', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-img' => true]]) !!}
                                    @endif
                                @endif
                            @else
                                {!! image_render(NULL, 'productTeaser_260_260', ['no_last_modify' => FALSE, 'only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_item->title), 'uk-img' => true]]) !!}
                            @endif
                    </a>
                </div>
                {{--<div class="uk-grid-collapse uk-child-width-1-2 uk-flex-middle uk-grid">--}}
                @if(($_param = ($_item->paramOptions[1] ?? NULL)))
                    <div class="product-marks">
                        @foreach($_param['options'] as $_option_id => $_option_item)
                            <button class="uk-button uk-button-link uk-position-relative uk-button-color-{!! $_option_id !!}"
                                    type="button">
                                {!! $_option_item['icon'] !!}
                            </button>
                            <div class="mark-color-{!! $_option_id !!}"
                                 uk-drop="pos: top-center; delay-hide:0;">
                                <div class="mark-color">
                                    <b>
                                        {!! $_option_item['title'] !!}
                                    </b>
                                    {!! $_option_item['sub_title'] !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                {{--<div class="uk-text-right">--}}
                {{--@if($_item->sale_statistics >= 1)--}}
                {{--@php--}}
                {{--if($_item->sale_statistics == 1){--}}
                {{--$_sale_statistics_text = trans('frontend.sale_statistics_text_1');--}}
                {{--}--}}
                {{--if($_item->sale_statistics > 1){--}}
                {{--$_sale_statistics_text = trans('frontend.sale_statistics_text_2');--}}
                {{--}--}}
                {{--if($_item->sale_statistics >= 5){--}}
                {{--$_sale_statistics_text = trans('frontend.sale_statistics_text_3');--}}
                {{--}--}}
                {{--@endphp--}}
                {{--<div class="product-story">--}}
                {{--{!! $_item->sale_statistics . ' ' . $_sale_statistics_text !!}--}}
                {{--</div>--}}
                {{--@endif--}}
                {{--</div>--}}
                {{--</div>--}}
            </div>
            <div class="uk-tile-bottom uk-flex-1 uk-flex uk-flex-column uk-flex-between">
                <div>
                    <div class="title-product uk-flex uk-flex-middle">
                        @l(str_limit(strip_tags($_item->title), 50), $_item->generate_url, ['attributes' => ['title' =>
                        strip_tags(str_replace([
                        "'",
                        '"'
                        ], '', $_item->title)), 'class' => 'uk-display-block uk-overflow-hidden']])
                    </div>
                    @if(($_param = ($_item->paramOptions[5] ?? NULL)))
                        <div class="consist uk-text-lowercase">
                            @php
                                $_param_values = NULL;
                                foreach($_param['options'] as $_option_id => $_option_item) $_param_values[] = $_option_item['title'];
                            @endphp
                            <div class="param-values uk-overflow-hidden">
                                {{ str_limit(implode(', ', $_param_values),78) }}
                            </div>
                        </div>
                    @endif
                </div>
                <div class="param-items">
                    @if(($_param = ($_item->paramOptions[6] ?? NULL)))
                        <div class="param-product uk-display-inline-block">
                            {!! $_param['options'] . ' ' . $_param['unit'] !!}
                            -
                        </div>
                    @endif
                    @if(($_param = ($_item->paramOptions[3] ?? NULL)))
                        <div class="param-product uk-display-inline-block">
                            {!! $_param['options'] . ' ' . $_param['unit'] !!}
                        </div>
                    @endif
                    @if(($_param = ($_item->paramOptions[4] ?? NULL)))
                        <div class="param-product uk-display-inline-block">
                            {!! $_param['options'] . ' ' . $_param['unit'] !!}
                        </div>
                    @endif
                    @include('frontend.default.shops.product_teaser_price')
                </div>
            </div>
            {{--<div class="shop-product-action-box-{{ $_item->id }}">--}}
            {{--@if($_item->price['view_price'])--}}
            {{--<div class="shop-product-action-box uk-margin-remove">--}}
            {{--<div class="box-shop-add uk-text-center uk-position-relative">--}}
            {{--<a href="{{ $_item->generate_url }}"--}}
            {{--rel="nofollow"--}}
            {{--class="uk-button uk-button-success uk-text-uppercase">--}}
            {{--@lang('frontend.button.view_availability')--}}
            {{--</a>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--@else--}}
            {{--<div class="box-shop-add">--}}
            {{--<button type="button"--}}
            {{--data-path="{{ _r('ajax.shop_notify_when_appears') }}"--}}
            {{--data-product="{{ $_item->id }}"--}}
            {{--class="uk-button uk-button-default uk-text-uppercase btn-appears uk-float-right use-ajax">--}}
            {{--Сообщить о наличии--}}
            {{--</button>--}}
            {{--</div>--}}
            {{--@endif--}}
            {{--</div>--}}
            @if(isset($_accessEdit['shop_product']) && $_accessEdit['shop_product'])
                <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
                    @if($_locale == DEFAULT_LOCALE)
                        @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.shop_products.edit', ['p' =>
                        ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block
                        uk-line-height-1']])
                    @else
                        @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.shop_products.translate', ['p' =>
                        ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' =>
                        'uk-display-block uk-line-height-1']])
                    @endif
                </div>
            @endif
        </div>
    </div>
</li>
