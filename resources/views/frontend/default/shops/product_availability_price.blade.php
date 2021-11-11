<div id="uk-product-availability-price-box-{{ $_item->id }}">
    @if($_items->isNotEmpty())
        <ul class="uk-nav-parent-icon nav-product-availability"
            uk-nav="multiple: true">
            @foreach($_items as $_prices)
                <li class="uk-parent">
                    {{--@if($loop->index) @else uk-open @endif--}}
                    <a href="#"
                       class="uk-text-uppercase">
                        {{ $_prices['location']->full_name }}
                    </a>
                    <ul class="uk-nav-sub">
                        @foreach($_prices['pharmacies'] as $_pharmacy)
                            <li>
                                <div class="uk-grid-small uk-flex uk-flex-middle"
                                     uk-grid>
                                    <div class="uk-width-expand@m">
                                        <div uk-grid
                                             class="uk-grid-small uk-flex uk-flex-middle">
                                            <div class="uk-width-1-5@xl uk-width-1-4@s uk-width-1-2 uk-margin-remove">
                                                @l($_pharmacy['pharmacy']->breadcrumb_title, $_pharmacy['pharmacy']->generate_url, ['attributes' => ['target' => '_blank', 'class' => 'pharmacy']])
                                            </div>
                                            <div class="uk-width-1-5@xl uk-width-1-4@s uk-width-1-2 item-nav uk-margin-remove">
                                                {{ $_pharmacy['pharmacy']->address }}
                                            </div>
                                            <div class="uk-width-1-5@xl uk-width-1-4@s uk-width-1-2 item-nav uk-margin-remove">
                                                {{ $_pharmacy['pharmacy']->working_hours }}
                                            </div>
                                            <div class="uk-width-1-5@xl uk-width-1-4@s uk-width-1-2 item-nav uk-margin-remove">
                                                @if($_pharmacy['pharmacy']->formation_phones->isNotEmpty())
                                                    {!! $_pharmacy['pharmacy']->formation_phones->pluck('format_render')->implode(',&nbsp;') !!}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="uk-width-large@xl uk-width-medium@m">
                                        @foreach($_pharmacy['count_in_stock'] as $_type => $_data)
                                            <div uk-grid
                                                 class="uk-grid-small uk-child-width-1-3 uk-flex uk-flex-middle shop-product-buy-box uk-margin-remove">
                                                <div class="number-counter-box">
                                                    <div class="uk-input-number-counter-box">
                                                        <button type="button"
                                                                class="uk-button"
                                                                name="decrement"
                                                                disabled>
                                                        </button>
                                                        <input
                                                            type="number"
                                                            value="1"
                                                            data-default="1"
                                                            min="1"
                                                            max="{{ $_pharmacy['count_in_stock'][$_type] }}"
                                                            step="1"
                                                            name="count"
                                                            class="uk-input"
                                                            autocapitalize="off">
                                                        <button type="button"
                                                                name="increment"
                                                                class="uk-button"
                                                            {{ $_pharmacy['count_in_stock'][$_type] == 1 ? 'disabled' : NULL }}>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="">
                                                    <div class="price-availability">
                                                        {{ $_pharmacy['price'][$_type]['format']['view_price'] }}
                                                        <span class="currency-suffix">{{ $_pharmacy['price'][$_type]['currency']['suffix'] }}</span>
                                                    </div>
                                                    @if($_type == 'part')
                                                        <div class="part-product uk-text-uppercase">
                                                            (@lang('shop.labels.buy_part', ['part' => $_pharmacy['multiplicity']]))
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="uk-text-center">
                                                    <button type="button"
                                                            data-path="{{ _r('ajax.shop_action_basket', ['shop_price' => $_pharmacy['id'][$_type]]) }}"
                                                            data-type="availability"
                                                            class="uk-button uk-button-link shop-product-buy-button">
                                                        <img src="{{ formalize_path('template/images/icon-basket-green.png') }}"
                                                             alt="">
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    {{--@if(!$loop->last)--}}
                    {{--<hr>--}}
                    {{--@endif--}}
                </li>
            @endforeach
        </ul>
    @else
        <div class="uk-alert uk-alert-warning">
            @lang('shop.notifications.not_availability')
        </div>
    @endif
</div>