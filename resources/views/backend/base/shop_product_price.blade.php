<div class="shop-product-action-box uk-flex uk-flex-middle"
     id="shop-product-action-box"
     uk-height-match="row: false">
    @if($_item->price['view_price'])
        <div class="uk-position-relative">
            <div class="uk-flex uk-flex-middle">
                <div class="uk-flex-1 uk-margin-small-right">
                    <div class="uk-h1 uk-flex-1 uk-margin-remove">
                        @if(count($_item->price['view']) > 1)
                            <strike class="uk-text-muted">{!! $_item->price['view'][0]['format']['view_price'] !!}</strike>
                            {!! $_item->price['view'][1]['format']['view_price_2'] !!}
                        @else
                            {!! $_item->price['view'][0]['format']['view_price_2'] !!}
                        @endif
                    </div>
                    <div class="uk-text-small">
                        {!! $_item->price['view_available'] !!}
                    </div>
                </div>
                <div class="uk-input-number-counter-box">
                    @php
                        $_max = $_item->price['status'] == 'in_stock' ? $_item->price['count_in_stock'] : NULL;
                    @endphp
                    <button type="button"
                            class="uk-button"
                            name="decrement"
                            disabled>
                        &ndash;
                    </button>
                    <input
                        type="number"
                        value="1"
                        data-default="1"
                        min="1"
                        max="{{ $_max }}"
                        step="1"
                        name="count"
                        class="uk-input"
                        autocapitalize="off">
                    <button type="button"
                            name="increment"
                            class="uk-button"
                        {{ $_max == 1 ? 'disabled' : NULL }}>
                        +
                    </button>
                </div>
            </div>
        </div>
        <div class="uk-flex uk-margin-left">
            <div class="uk-margin-left">
                <button type="button"
                        id="shop-product-buy-button"
                        data-path="{{ _r('ajax.shop_action_basket', ['shop_product' => $_item]) }}"
                        class="uk-button uk-button-success uk-button-large uk-height-1-1 shop-product-buy-button">
                    <span uk-icon="add_shopping_cart"></span>
                </button>
            </div>
            <div class="uk-margin-left">
                <button type="button"
                        data-path="{{ _r('ajax.shop_buy_one_click') }}"
                        data-product="{{ $_item->id }}"
                        class="uk-button uk-text-uppercase uk-text-bold uk-button-color-amber uk-height-1-1 shop-buy-one-click use-ajax">
                    @lang('forms.buttons.buy.one_click')
                </button>
            </div>
        </div>
    @else
        <div class="uk-flex-1">
            <div class="uk-h1 uk-flex-1 uk-margin-remove">
                @if(count($_item->price['view']) > 1)
                    <strike class="uk-text-muted">{!! $_item->price['view'][0]['format']['view_price'] !!}</strike>
                    {!! $_item->price['view'][1]['format']['view_price_2'] !!}
                @else
                    {!! $_item->price['view'][0]['format']['view_price_2'] !!}
                @endif
            </div>
            <div class="uk-text-small">
                {!! $_item->price['view_available'] !!}
            </div>
        </div>
        @if(!$_item->price['count_in_basket'])
            <div class="uk-flex uk-margin-left">
                <button type="button"
                        data-path="{{ _r('ajax.shop_notify_when_appears') }}"
                        data-product="{{ $_item->id }}"
                        class="uk-button uk-button-bordered uk-border-double-add uk-border-color-blue uk-line-height-1 uk-height-1-1 uk-button-large uk-text-uppercase uk-padding-small uk-button-color-hover-blue uk-padding-medium-right uk-padding-medium-left use-ajax">
                    @lang('forms.buttons.buy.notify_when_appears')
                </button>
            </div>
        @endif
    @endif
</div>