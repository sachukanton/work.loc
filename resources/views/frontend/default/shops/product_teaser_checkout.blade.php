@if($_item instanceof \App\Models\Shop\Product)
    @php
        global $wrap;
        $_mark = $_item->mark[0] ?? NULL;
        $_mark_hit = $_item->paramOptions[5] ?? NULL;
        if($_mark_hit){
        foreach ($_mark_hit['options'] as $_option_id => $_option_item){
        $_mark_hit_id = 'mark-hit-' . $_option_id;
        }
        }
        $_device_type = $wrap['device']['type'] ?? 'pc';
    @endphp
    <div class="swiper-slide">
        <div class="category__open_item dom-item-card-product-{{ $_item->modify }}">
            <div class="category__open_item--img {{$_mark_hit_id ?? NULL}} product-id-{{ $_item->id }} @if($_item->price['count_in_basket'] >= 1) add-basket @endif">
                    <div>
                        @if(($_param = ($_item->paramOptions[5] ?? NULL)))
                            @foreach($_param['options'] as $_option_id => $_option_item)
                                <div class="product-marks-hit"
                                     style="background-color: {{ '#' . $_option_item['attribute']}}">
                                    {!! $_option_item['title'] !!}
                                </div>
                            @endforeach
                        @endif
                        @if(($_param = ($_item->paramOptions[1] ?? NULL)))
                            <div class="tag">
                                @foreach($_param['options'] as $_option_id => $_option_item)
                                    {!! $_option_item['icon'] !!}
                                @endforeach
                            </div>
                        @endif

                    </div>
                    <div class="preview-product uk-margin-auto uk-flex uk-flex-center uk-flex-middle">
                        <a href="{{ $_item->generate_url }}"
                           rel="nofollow">

                            <div id="product-{{ $_item->id }}"
                                 class="shop-product-change-images">
                                @if($_item->preview_fid || $_item->full_fid)


                                    @if($_item->full_fid)
                                    {!! image_render($_item->_preview_full, 'productTeaser_344_319', ['attributes' => ['title' => strip_tags($_item->title), 'alt' => strip_tags($_item->title), 'uk-img' => TRUE]]) !!}
                                    @else
                                    @if($_item->preview_fid)
                                    {!! $_item->_preview_asset('productTeaser_344_319', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-img' => TRUE]]) !!}
                                    @endif
                                    @endif


                                    @if($_device_type == 'mobile')
                                        <div class="preview-fid-mb uk-flex uk-flex-center uk-flex-middle uk-position-relative">
                                            @if($_item->preview_fid)
                                                {!! $_item->_preview_asset('productTeaser_150_100', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-img' => TRUE, 'uk-cover' => TRUE]]) !!}
                                            @endif
                                        </div>
                                    @else
                                        <div class="preview-fid uk-flex uk-flex-center uk-flex-middle uk-position-relative">
                                            @if($_item->preview_fid)
                                                {!! $_item->_preview_asset('productTeaser_300_200', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-img' => TRUE, 'uk-cover' => TRUE]]) !!}
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    {!! image_render(NULL, 'productTeaser_320_320', ['no_last_modify' => FALSE, 'only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_item->title), 'uk-img' => TRUE]]) !!}
                                @endif
                                {{--@if($_item->full_fid)--}}
                                {{--<div class="full-fid">--}}
                                {{--{!! image_render($_item->_preview_full, 'productTeaser_344_319', ['attributes' => ['title' => strip_tags($_item->title), 'alt' => strip_tags($_item->title), 'uk-img' => TRUE]]) !!}--}}
                                {{--</div>--}}
                                {{--@endif--}}
                            </div>
                            {{--@include('frontend.default.shops.view_list_product_image', ['_item' => $_product])--}}

                        </a>
                    </div>
            </div>
            <div class="category__open_item--info">
                <div>
                    <h6 class="open_title">@l(str_limit(strip_tags($_item->title), 50), $_item->generate_url, ['attributes' => ['title' =>
                    strip_tags(str_replace([
                    "'",
                    '"'
                    ], '', $_item->title))]])</h6>
                </div>
                <div class="category__open_item--more">
                    <div class="wrapperes">
                        <p>
                        @if(($_param = ($_item->paramOptions[4] ?? NULL)))
                           
                                {!! $_param['options'] . ' ' . $_param['unit'] !!}
                                -&nbsp;
                            
                        @endif
                        @if(($_param = ($_item->paramOptions[3] ?? NULL)))
                            
                                {!! $_param['options'] . ' ' . $_param['unit'] !!}
                           
                        @endif
                        @if(($_param = ($_item->paramOptions[7] ?? NULL)))
                           
                                {!! $_param['options'] . ' ' . $_param['unit'] !!}
                            
                        @endif
                        </p>

                        <div class="price">
                            @if($_item->price['view_price'])
                            @if(count($_item->price['view']) > 1)
                                <div class="old_price">{!! $_item->price['view'][0]['format']['view_price'] !!}</div>
                                <div class="real-price">
                                    {!! $_item->price['view'][1]['format']['view_price_2'] !!}
                                </div>
                            @else
                                <div class="real-price">
                                    {!! $_item->price['view'][0]['format']['view_price_2'] !!}
                                </div>
                            @endif
                            @else
                                <div class="product-not-available">
                                    @lang('frontend.not_available')
                                </div>
                            @endif
                        </div>
                    </div>
                    @if(($_param = ($_item->paramOptions[2] ?? NULL)))                    
                            @php
                                $_param_values = NULL;
                                foreach($_param['options'] as $_option_id => $_option_item) $_param_values[] = $_option_item['title'];
                            @endphp
                            <p>
                                {{ str_limit(implode(', ', $_param_values),60) }}
                            </p>
                    @endif
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
                <div class="btn__wrapper">
                    <button type="button"
                            data-path="{{ _r('ajax.shop_buy_one_click') }}"
                            data-product="{{ $_item->id }}"
                            class="btn--white use-ajax">
                        {{variable('one_click')}}
                    </button>
                    <button type="button"
                        data-path="{{ _r('ajax.shop_action_basket', ['shop_price' => $_item->price['id']]) }}"
                        data-type="teaser"
                        data-spicy="{{ (int) $_item->is_spicy }}"
                        class="btn btn-cart-product-{{ $_item->id }} use-ajax">
                        <svg>
                            <use xlink:href="#bike"></use>
                        </svg>
                        {{variable('cart')}}
                    </button>
                </div>
            </div>
        </div>
        @if(isset($_accessEdit['shop_product']) && $_accessEdit['shop_product'])
            <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right uk-position-z-index">
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
        @if($_item->js_modification_items && $_item->modification_items && $_item->modification_items->count() > 1)
            <script>
                if (typeof catalogViewPush == "function") catalogViewPush({!! json_encode($_item->js_modification_items) !!});
            </script>
        @endif
    </div>
               <!--  @if(isset($_accessEdit['shop_product']) && $_accessEdit['shop_product'])
                    <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right uk-position-z-index">
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
                @endif -->
        
@elseif($_item)
    <div class="category__open_item banner">
        {!! $_item->link ? '<a href="'. _u($_item->link) .'" '. ($_item->link_attributes ?: NULL) .' class="">' : NULL !!}
        @if($_item->background_fid)
            {!! $_item->_background_asset(NULL, ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-img' => TRUE]]) !!}
        @else
            {!! content_render($_item) !!}
        @endif
        {!! $_item->link ? '</a>' : NULL !!}
    </div>
@endif
