<div class="shop-product-action-box uk-text-center">
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
    @else
        <div class="view-available uk-margin-remove">
            {!! $_item->price['view_available'] !!}
        </div>
    @endif
</div>
<div class="uk-flex uk-flex-center uk-flex-middle uk-margin-small-top">
    <div class="uk-input-number-counter-box">
        @php
            $_max = $_item->price['status'] == 'in_stock' ? $_item->price['quantity_max'] : NULL;
        @endphp
        <button type="button"
                class="uk-button"
                name="decrement"
            {{ $_item->quantity == 1 ? 'disabled' : NULL }}>
            &ndash;
        </button>
        <input
            type="number"
            value="{{ $_item->quantity }}"
            min="1"
            max="{{ $_max }}"
            data-default="1"
            data-callback="recountBasketProducts"
            data-product="{{ $_item->id }}"
            step="1"
            name="count"
            class="uk-input"
            autocapitalize="off">
        <button type="button"
                name="increment"
                class="uk-button"
            {{ $_max == $_item->quantity ? 'disabled' : NULL }}>
            +
        </button>
    </div>
    <div class="uk-margin-left">
        <a href="{{ _r('ajax.checkout_remove_products') }}"
           rel="nofollow"
           data-item="{{ $_item->id }}"
           data-page="1"
           uk-icon="icon: clearclose"
           class="uk-icon-link uk-link-color-red uk-remove-product use-ajax"></a>
    </div>
</div>