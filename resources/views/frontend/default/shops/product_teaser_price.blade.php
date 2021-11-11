<div class="shop-product-action-box-{{ $_item->id }}">
    {{--<div class="card-view-available uk-text-uppercase">--}}
        {{--{!! $_item->price['view_available'] !!}--}}
    {{--</div>--}}
    @if($_item->price['view_price'])
        <div class="uk-flex-bottom uk-grid-collapse uk-grid">
            <div class="uk-width-expand">
                <div class="price-product">
                    @if(count($_item->price['view']) > 1)
                        <strike class="old-price">{!! $_item->price['view'][0]['format']['view_price'] !!}</strike>
                        <div class="real-price">
                            {!! $_item->price['view'][1]['format']['view_price_2'] !!}
                        </div>
                    @else
                        <div class="real-price">
                            {!! $_item->price['view'][0]['format']['view_price_2'] !!}
                        </div>
                    @endif
                </div>
            </div>
            <div class="uk-width-auto">
                <div class="btn-active-product">
                <button type="button"
                        data-path="{{ _r('ajax.shop_action_basket', ['shop_price' => $_item->price['id']]) }}"
                        data-type="teaser"
                        class="uk-button uk-button-default uk-position-relative btn-product use-ajax">
                    @lang('frontend.button.order')
                </button>
                </div>
            </div>
        </div>
    {{--@elseif($_item->price['pharmacy_min_price_exist'])--}}
        {{--<div class="uk-flex uk-flex-bottom shop-product-action-box uk-margin-remove">--}}
            {{--<div class="uk-flex-1">--}}
                {{--<div class="uk-margin-remove uk-display-inline-block price">--}}
                    {{--<div class="price-format">--}}
                        {{--<span class="from currency-suffix">--}}
                             {{--@lang('frontend.from')--}}
                        {{--</span>--}}
                        {{--<span class="">--}}
                            {{--{!! $_item->price['pharmacy_min_price']['format']['view_price_2'] !!}--}}
                        {{--</span>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="box-shop-add uk-text-center uk-position-relative">--}}
                {{--<a href="{{ $_item->generate_url }}"--}}
                   {{--class="uk-button uk-button-success uk-text-uppercase">--}}
                    {{--@lang('forms.buttons.buy.view_availability')--}}
                {{--</a>--}}
            {{--</div>--}}
        {{--</div>--}}
    @else
        <div class="product-not-available">
            @lang('frontend.not_available')
        </div>
        {{--<div class="box-shop-add">--}}
            {{--<button type="button"--}}
                    {{--data-path="{{ _r('ajax.shop_notify_when_appears') }}"--}}
                    {{--data-product="{{ $_item->id }}"--}}
                    {{--class="uk-button uk-button-default uk-text-uppercase btn-appears uk-float-right use-ajax">--}}
                {{--Сообщить о наличии--}}
            {{--</button>--}}
        {{--</div>--}}
    @endif
</div>