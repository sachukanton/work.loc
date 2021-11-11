@php
//    global $wrap;
//   $_device_type = $wrap['device']['type'] ?? 'pc';
@endphp
<div class="swiper-slide">
        <div class="wrappers">
                @if ($_item->status == 1)
                <a href="{{ $_item->generate_url }}"
                   rel="nofollow">
                    @if($_item->preview_fid || $_item->full_fid)
                            @if($_item->full_fid)
                                {!! image_render($_item->_preview_full, 'productTeaser_140_140', ['attributes' => ['title' => strip_tags($_item->title), 'alt' => strip_tags($_item->title), 'uk-img' => TRUE]]) !!}
                            @else
                                @if($_item->preview_fid)
                                    {!! $_item->_preview_asset('productTeaser_140_140', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-img' => TRUE]]) !!}
                                @endif
                            @endif
                    @else
                        {!! image_render(NULL, 'productTeaser_140_140', ['no_last_modify' => FALSE, 'only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_item->title), 'uk-img' => TRUE]]) !!}
                    @endif
                </a>
                @else
                <div>
                    @if($_item->preview_fid || $_item->full_fid)
                            @if($_item->full_fid)
                                {!! image_render($_item->_preview_full, 'productTeaser_140_140', ['attributes' => ['title' => strip_tags($_item->title), 'alt' => strip_tags($_item->title), 'uk-img' => TRUE]]) !!}
                            @else
                                @if($_item->preview_fid)
                                    {!! $_item->_preview_asset('productTeaser_140_140', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-img' => TRUE]]) !!}
                                @endif
                            @endif
                    @else
                        <img src="images/no-image.png" alt="">
                    @endif
                </div>
                @endif
            <div class="teaser-consist uk-flex uk-flex-bottom">
                <div>
                <div>
                    @if ($_item->status == 1)
                    <div class="uk-flex uk-flex-middle">
                        @l(str_limit(strip_tags($_item->title), 20), $_item->generate_url, ['attributes' => ['title' =>
                        strip_tags(str_replace([
                        "'",
                        '"'
                        ], '', $_item->title))]])
                    </div>
                    @else
                    <div class="uk-flex uk-flex-middle">
                       <span>{{ str_limit($_item->title ,20) }}</span>
                    </div>
                    @endif
                    @if(($_param = ($_item->paramOptions[2] ?? NULL)))
                        <div class="consist-card">
                            @php
                                $_param_values = NULL;
                                foreach($_param['options'] as $_option_id => $_option_item) $_param_values[] = $_option_item['title'];
                            @endphp
                            {{--<div class="param-values uk-overflow-hidden">--}}
                                {{--{{ str_limit(implode(', ', $_param_values),96) }}--}}
                            {{--</div>--}}
                        </div>
                    @endif
                </div>
                    {{--@if(($_param = ($_item->paramOptions[4] ?? NULL)))--}}
                        {{--<div class="param-consist uk-display-inline-block">--}}
                            {{--{!! $_param['options'] . ' <span>' . $_param['unit'] . '</span>' !!}--}}
                        {{--</div>--}}
                    {{--@endif--}}
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
        </div>


    @if($_item->js_modification_items && $_item->modification_items && $_item->modification_items->count() > 1)
        <script>
            if (typeof catalogViewPush == "function") catalogViewPush({!! json_encode($_item->js_modification_items) !!});
        </script>
    @endif
</div>
