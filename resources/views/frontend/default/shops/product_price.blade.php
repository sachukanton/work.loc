<div class="shop-product-action-box uk-flex uk-flex-bottom uk-flex-wrap"
     id="shop-product-action-box">
    {{--<div class="uk-h5 uk-margin-remove-top uk-line-height-1 uk-padding-remove uk-text-uppercase uk-margin-small-bottom">--}}
    {{--{!! $_item->price['view_available'] !!}--}}
    {{--</div>--}}
    @if($_item->price['view_price'])
        <div>
            @if(count($_item->price['view']) > 1)
                <div class="old-price-format">
                    <strike>{!! $_item->price['view'][0]['format']['view_price'] !!}</strike>
                </div>
                <div class="real-old price-format uk-margin-remove">
                    {!! $_item->price['view'][1]['format']['view_price_2'] !!}
                </div>
            @else
                <div class="real-old price-format uk-margin-remove">
                    {!! $_item->price['view'][0]['format']['view_price_2'] !!}
                </div>
            @endif
        </div>
        <div class="uk-flex-1 uk-flex uk-flex-center uk-flex-middle">
            <div class="uk-input-number-counter-box uk-flex uk-flex-middle uk-position-relative">
                <button type="button"
                        class="uk-button uk-display-block uk-position-relative"
                        name="decrement"
                        disabled>
                    -
                </button>
                <div class="uk-flex uk-flex-column uk-flex-middle uk-flex-center input-number">
                    <input
                        type="number"
                        value="1"
                        data-default="1"
                        min="1"
                        max="10000000"
                        step="1"
                        name="count"
                        class="uk-input uk-disabled"
                        autocapitalize="off">
                    <div class="count-text uk-text-uppercase">
                        в заказе
                    </div>
                </div>
                <button type="button"
                        name="increment"
                        class="uk-button uk-display-block uk-position-relative">
                    +
                </button>
                {{--<div class="uk-input-number-counter-button-col">--}}
                {{--</div>--}}
            </div>
            <button type="button"
                    id="shop-product-buy-button"
                    data-path="{{ _r('ajax.shop_action_basket', ['shop_price' => $_item->price['id']]) }}"
                    class="uk-button uk-button-buy uk-text-uppercase">
                @lang('frontend.button.order')
            </button>
        </div>
        {{--<div class="uk-flex uk-margin-left">--}}
        {{--<div class="uk-margin-left">--}}
        {{--<button type="button"--}}
        {{--data-path="{{ _r('ajax.shop_buy_one_click') }}"--}}
        {{--data-product="{{ $_item->id }}"--}}
        {{--class="uk-button uk-text-uppercase uk-text-bold uk-button-color-amber uk-height-1-1 shop-buy-one-click use-ajax">--}}
        {{--@lang('forms.buttons.buy.one_click')--}}
        {{--</button>--}}
        {{--</div>--}}
        {{--</div>--}}
    @else
        {{--        @if($_item->price['pharmacy_min_price_exist'])--}}
        {{--            <div class="price-format uk-margin-remove-top uk-margin-bottom">--}}
        {{--                {!! $_item->price['pharmacy_min_price']['format']['view_price_2'] !!}--}}
        {{--            </div>--}}
        {{--        @endif--}}
        {{--        <button type="button"--}}
        {{--                data-path="{{ _r('ajax.shop_notify_when_appears') }}"--}}
        {{--                data-product="{{ $_item->id }}"--}}
        {{--                class="uk-button uk-button-large uk-text-uppercase uk-animation-toggle use-ajax">--}}
        {{--                <span uk-icon="icon:notifications_active"--}}
        {{--                      class="uk-animation-swing uk-margin-small-right"></span>--}}
        {{--            @lang('forms.buttons.notify_when_appears.open')--}}
        {{--        </button>--}}
    @endif
</div>
