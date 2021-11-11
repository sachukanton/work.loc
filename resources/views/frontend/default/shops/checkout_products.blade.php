@php
    global $wrap;
    $_device_type = wrap()->get('device.type');
@endphp
<div id="form-checkout-order-products">
    @if(isset($_items) && $_items)
        <div class="cart__main--items-wrapper">
            @foreach($_items as $_key => $_product)
                @foreach($_product->composition as $_comp)
                    <div class="cart__main--item">
                            {{--@if($_device_type != 'mobile')--}}
                                    <div class="cart__main_img">
                                        @if($_product->preview_fid)
                                            {{--@if($_product->full_fid)--}}
                                                {{--{!! image_render($_product->_preview_full, 'productTeaser_188_125', ['attributes' => ['title' => $_product->title, 'alt' => $_product->title]]) !!}--}}
                                            {{--@else--}}
                                                {!! image_render($_product->_preview, NULL, ['attributes' => ['title' => $_product->title, 'alt' => $_product->title]]) !!}
                                            {{--@endif--}}
                                        @else
                                            {!! image_render(NULL, 'productTeaser_188_125', ['no_last_modify' => FALSE, 'only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_product->title), 'width' => 140]]) !!}
                                        @endif
                                    </div>
                            {{--@endif--}}
                            <div class="cart__main_name">
                                @l(str_limit(strip_tags($_product->title), 50) . ($_comp['key'] === 'certificate' ? ' [СЕРТИФИКАТ]' : null), $_product->generate_url,
                                ['attributes' => ['title' => strip_tags(str_replace(["'",'"'], '',
                                $_product->title)), 'class' => '']])
                            </div>
                            <div class="cart__main_size">
                                @if(($_param = ($_product->paramOptions[3] ?? NULL)))
                                    <div id="shop-product-weight-box">
                                        {{--{{ $_param['title'] }}--}}
                                        {{ $_param['options'] . ($_param['unit'] ? "{$_param['unit']}" : NULL) }}
                                    </div>
                                @endif
                                @if(($_param = ($_product->paramOptions[4] ?? NULL)))
                                    <div>
                                        @if($_product->paramOptions[3] ?? NULL)&nbsp;-&nbsp;@endif
                                        {{ $_param['options'] . ($_param['unit'] ? " {$_param['unit']}" : NULL) }}
                                    </div>
                                @endif
                                @if(($_param = ($_product->paramOptions[6] ?? NULL)))
                                    {!! $_param['options'] . ' ' . $_param['unit'] !!}
                                @endif
                            </div>
                            <!-- <div class="cart__main_price-old">
                                @if($_product->price_certificate && $_comp['key'] === 'certificate')
                                    <span style="text-decoration: line-through;"
                                          class="uk-text-danger">{!! $_product->price['format']['view_price'] !!}</span>
                                    {!! $_product->price_certificate['format']['view_price_2'] !!}
                                @else
                                    {!! $_product->price['format']['view_price_2'] !!}
                                @endif
                            </div> -->
                            <!-- @if(($_param = ($_product->paramOptions[2] ?? NULL)))
                                <div class="consist-checkout">
                                    @php
                                        $_param_values = NULL;
                                        foreach($_param['options'] as $_option_id => $_option_item) $_param_values[] = $_option_item['title'];
                                    @endphp
                                    <div class="param-values uk-overflow-hidden">
                                        {{ str_limit(implode(', ', $_param_values),150) }}
                                    </div>
                                </div>
                            @endif -->
                                <div class="input uk-input-number-counter-box">
                                    <input class="sum"
                                            type="number"
                                            value="{{ $_comp['quantity'] }}"
                                            min="1"
                                            data-default="1"
                                            data-callback="recountBasketProducts"
                                            data-e="{{ "{$_product->price_id}::{$_comp['spicy']}::{$_comp['key']}" }}"
                                            step="1"
                                            name="count"
                                            {{ $_comp['key'] === 'certificate' ? 'disabled' : NULL }}
                                            class="uk-input uk-text-center uk-disabled"
                                            autocapitalize="off">
                                    <div class="range">
                                        <button type="button"
                                                name="increment"
                                                class="plus"
                                                {{ $_comp['key'] === 'certificate' ? 'disabled' : NULL }}>
                                            +
                                        </button>
                                        <button type="button"
                                                class="minus"
                                                {{ $_comp['quantity'] == 1 ? 'disabled' : NULL }}
                                                name="decrement">
                                            -
                                        </button>
                                    </div>
                                </div>
                                <div class="cart__main_price">
                                    @if($_product->price_certificate && $_comp['key'] === 'certificate')
                                        <span style="text-decoration: line-through;"
                                              class="uk-text-danger">{!! $_product->price['format']['view_price'] !!}</span>
                                        {!! $_product->price_certificate['format']['view_price_2'] !!}
                                    @else
                                        {!! $_product->price['format']['view_price_2'] !!}
                                    @endif
                                </div>
                                <div class="cart__main_price-old">
                                    {!! $_product->_price->old_price !!}
                                </div>
                                <a href="{{ _r('ajax.checkout_remove_products') }}"
                                   rel="nofollow"
                                   data-e="{{ "{$_product->price_id}::{$_comp['spicy']}::{$_comp['key']}" }}"
                                   class="trash">
                                    <svg>
                                        <use xlink:href="#trash"></use>
                                    </svg>
                                </a>
                    </div>
                @endforeach
            @endforeach
        </div>
        <div class="checkout-warning-text">
            @variable('warning_text_on_checkout_page')
        </div>
    @else
        <div class="uk-alert uk-alert-warning">
            @lang('frontend.basket_is_empty')
        </div>
    @endif
</div>
