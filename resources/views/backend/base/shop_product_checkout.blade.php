<div class="uk-position-relative uk-height-1-1 uk-padding-small">
    <div class="uk-height-1-1 uk-flex uk-flex-column card-item uk-position-relative">
        <div class="uk-card uk-card-small uk-card-default uk-border-rounded uk-box-shadow-small uk-height-1-1 uk-overflow-hidden">
            <div class="uk-card-media-top">
                <a href="{{ $_item->generate_url }}"
                   rel="nofollow">
                    <div class="uk-cover-container uk-height-small">
                        @if($_item->preview_fid)
                            {!! $_item->_preview_asset('productTeaser_300_300', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_item->title), 'uk-cover' => TRUE]]) !!}
                        @else
                            {!! image_render(NULL, 'productTeaser_300_300', ['no_last_modify' => FALSE, 'only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_item->title), 'uk-cover' => TRUE]]) !!}
                        @endif
                    </div>
                </a>
            </div>
            <div class="uk-card-body">
                <div class="uk-text-small uk-text-primary">
                    SKY: {{ $_item->sky }}
                </div>
                <div class="uk-h3 uk-card-title uk-margin-remove-top uk-margin-small-bottom">
                    @l(str_limit(strip_tags($_item->title), 20), $_item->generate_url, ['attributes' => ['title' => strip_tags($_item->title), 'class' => 'uk-text-color-teal']])
                </div>
                @php
                    $_comment_rates = $_item->comment_rates;
                @endphp
                @if(is_array($_comment_rates['markup']))
                    {!! $_comment_rates['markup']['review'] !!}
                @else
                    {!! $_comment_rates['markup'] !!}
                @endif
                @if($_item->paramOptions)
                    <div>
                        <dl class="uk-description-list">
                            @foreach($_item->paramOptions as $_param)
                                <dt class="uk-margin-remove">
                                    {{ $_param['title'] }}:
                                </dt>
                                <dd class="uk-text-small">
                                    {!! implode(', ', $_param['options']) !!}
                                </dd>
                            @endforeach
                        </dl>
                    </div>
                @endif
                <hr>
                @include('backend.base.shop_product_checkout_price')
            </div>
        </div>
    </div>
    @if(isset($_accessEdit['shop_product']) && $_accessEdit['shop_product'])
        <div class="uk-position-absolute uk-position-top-right uk-margin-small-top uk-margin-small-right">
            @if($_locale == DEFAULT_LOCALE)
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.shop_products.edit', ['p' => ['id' => $_item->id], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @else
                @l('<span uk-icon="icon: settings; ratio: .7"></span>', 'oleus.shop_products.translate', ['p' => ['id' => $_item->id, 'locale' => $_locale], 'attributes' => ['target' => '_blank', 'class' => 'uk-display-block uk-line-height-1']])
            @endif
        </div>
    @endif
</div>