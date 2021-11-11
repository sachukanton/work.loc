<div class="uk-margin {{ isset($_accessEdit['shop_product']) && $_accessEdit['shop_product'] ? ' uk-position-relative' : NULL }}">
    <div class="uk-grid-small uk-flex uk-child-width-1-2">
        <div class="uk-position-relative">
            <a href="{{ $_item->generate_url }}"
               rel="nofollow">
                @if($_item->preview_fid)
                    {!! $_item->_preview_asset('shopProductThumb_200_200', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title)]]) !!}
                @else
                    {!! image_render(NULL, 'shopProductThumb_200_200', ['no_last_modify' => FALSE, 'only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_item->title)]]) !!}
                @endif
            </a>
            @if($_mark = $_item->mark)
                <div class="uk-text-uppercase uk-text-bold uk-position-top-left uk-padding-small-left uk-padding-small-top">
                    @if(in_array('hit', $_mark))
                        <span class="uk-padding-xsmall uk-border-rounded uk-text-color-white uk-background-color-green uk-display-inline-block uk-margin-small-right">Hit</span>
                    @endif
                    @if(in_array('new', $_mark))
                        <span class="uk-background-color-amber uk-border-rounded uk-padding-xsmall uk-display-inline-block uk-margin-small-right">New</span>
                    @endif
                    @if(in_array('discount', $_mark))
                        <span class="uk-background-color-red uk-text-color-white uk-border-rounded uk-padding-xsmall uk-display-inline-block">Discount</span>
                    @endif
                </div>
            @endif
        </div>
        <div>
            @php
                $_comment_rates = $_item->comment_rates;
            @endphp
            @if(is_array($_comment_rates['markup']))
                {!! $_comment_rates['markup']['review'] !!}
            @else
                {!! $_comment_rates['markup'] !!}
            @endif
            <div class="uk-h3 uk-card-title uk-margin-remove-top uk-margin-small-bottom">
                @l(str_limit(strip_tags($_item->title), 20), $_item->generate_url, ['attributes' => ['title' => strip_tags($_item->title), 'class' => 'uk-text-color-teal']])
            </div>
            <div class="uk-text-small uk-text-primary">
                SKY: {{ $_item->sky }}
            </div>
            @if($_item->paramOptions)
                <div>
                    <dl class="uk-description-list uk-description-list-horizontal">
                        @foreach($_item->paramOptions as $_param)
                            <div class="uk-text-color-grey lighten-1">
                                <span>{{ $_param['title'] }}:</span>
                                <span>{!! implode(', ', $_param['options']) !!}</span>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif
            @include('frontend.default.shops.product_teaser_price')
        </div>
    </div>
    @if(isset($_accessEdit['shop_product']) && $_accessEdit['shop_product'])
        <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right uk-border-rounded uk-border-double-add uk-border-color-white uk-background-color-white">
            @if($_locale == DEFAULT_LOCALE)
                @l('<span uk-icon="icon: createmode_editedit; ratio: .7"></span>', 'oleus.shop_products.edit', ['p' => ['shop_product' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-button uk-button-color-amber uk-button-small']])
            @else
                @l('<span uk-icon="icon: createmode_editedit; ratio: .7"></span>', 'oleus.shop_products.translate', ['p' => ['shop_product' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-button uk-button-color-amber uk-button-small']])
            @endif
        </div>
    @endif
</div>