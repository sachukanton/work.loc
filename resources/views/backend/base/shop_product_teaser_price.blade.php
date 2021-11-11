<div class="uk-flex uk-flex-middle shop-product-action-box uk-margin-remove shop-product-action-box-{{ $_item->id }}">
    <div class="uk-flex-1">
        @if($_item->price['view_price'] || $_item->price['count_in_basket'])
            <div class="uk-margin-remove uk-display-inline-block price">
                @if(count($_item->price['view']) > 1)
                    <strike>{!! $_item->price['view'][0]['format']['view_price'] !!}</strike>
                    <span class="real-old price-format">
                    {!! $_item->price['view'][1]['format']['view_price_2'] !!}
                    </span>
                @else
                    <span class="price-format">
                    {!! $_item->price['view'][0]['format']['view_price_2'] !!}
                    </span>
                @endif
            </div>
            <div class="view-available uk-margin-remove">
                {!! $_item->price['view_available'] !!}
            </div>
        @else
            <div class="uk-h4 uk-margin-remove uk-line-height-1 uk-padding-remove">
                {!! $_item->price['view_available'] !!}
            </div>
        @endif
    </div>
    <div class="box-shop-add uk-text-center uk-position-relative">
        @if($_item->price['view_price'])
            <button type="button"
                    data-path="{{ _r('ajax.shop_action_basket', ['shop_product' => $_item]) }}"
                    data-type="teaser"
                    class="uk-button uk-button-success uk-overflow-hidden use-ajax">
                <span uk-icon="icon:shopping_basket"></span>
            </button>
        @elseif(!$_item->price['count_in_basket'])
            <button type="button"
                    class="uk-button uk-button-color-blue uk-margin-small-left uk-height-1-1">
                <span uk-icon="icon:notifications_active; ratio: 1.3;"></span>
            </button>
        @endif
    </div>
</div>