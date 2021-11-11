<div id="wish-list-items">
    @if($_item->_items->isNotEmpty())
        @foreach($_item->_items as $_list)
            <div class="wish-list-item"
                 id="wish-list-item-{{ $_list->id }}">
                <div class="header-sidebar">
                    <div class="flex">
                        <div class="header {{ $_list->quantity_in == 0 ? 'empty' : NULL }}">
                            <span title="{{ $_list->name }}">
                                {{ $_list->name }}
                            </span>
                            <input type="text"
                                   value="{{ $_list->name }}">
                        </div>
                        <div class="info hidden-xs">
                            @if($_list->quantity_in)
                                {!! trans('forms.labels.checkout.total_amount_2', ['product' => plural_string($_list->quantity_in, 'shop.product.not_plural|shop.product.plural|shop.product.plurals|shop.product.plurals2'), 'amount' => '<span>' . $_list->amount['format']['view_price'] . '</span> ' . $_list->amount['currency']['suffix']]) !!}
                            @else
                                @lang('shop.alerts.empty')
                            @endif
                        </div>
                        <div class="link hidden-sm hidden-xs">
                            <button type="button"
                                    rel="rename-list"
                                    data-list="{{ $_list->id }}">
                                @lang('forms.buttons.wish_list.rename_list')
                            </button>
                            <button type="button"
                                    rel="remove-list"
                                    class="use-ajax"
                                    data-path="{{ _r('ajax.remove_wish_list', ['list' => $_list->id]) }}"
                                    data-list="{{ $_list->id }}">
                                @lang('forms.buttons.wish_list.remove_list')
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mobile-header-top visible-sm visible-xs">
                    <div class="link">
                        <button type="button"
                                rel="rename-list"
                                data-list="{{ $_list->id }}">
                            @lang('forms.buttons.wish_list.rename_list')
                        </button>
                        <button type="button"
                                rel="remove-list"
                                class="use-ajax"
                                data-path="{{ _r('ajax.remove_wish_list', ['list' => $_list->id]) }}"
                                data-list="{{ $_list->id }}">
                            @lang('forms.buttons.wish_list.remove_list')
                        </button>
                    </div>
                    <div class="flex">
                        <div class="info visible-xs">
                            @if($_list->quantity_in)
                                {!! trans('forms.labels.checkout.total_amount_2', ['product' => plural_string($_list->quantity_in, 'shop.product.not_plural|shop.product.plural|shop.product.plurals|shop.product.plurals2'), 'amount' => '<span>' . $_list->amount['format']['view_price'] . '</span> ' . $_list->amount['currency']['suffix']]) !!}
                            @else
                                @lang('shop.alerts.empty')
                            @endif
                        </div>
                        <div class="link">
                            <button type="button"
                                    rel="rename-list"
                                    data-list="{{ $_list->id }}">
                                @lang('forms.buttons.wish_list.rename_list')
                            </button>
                            <button type="button"
                                    rel="remove-list"
                                    class="use-ajax"
                                    data-path="{{ _r('ajax.remove_wish_list', ['list' => $_list->id]) }}"
                                    data-list="{{ $_list->id }}">
                                @lang('forms.buttons.wish_list.remove_list')
                            </button>
                        </div>
                    </div>
                </div>
                @foreach($_list->_products as $_product)
                    <div class="row product-sidebar"
                         id="wish-list-item-{{ $_list->id }}-product-{{ $_product->id }}">
                        <div class="col-md-2 image hidden-sm hidden-xs">
                            <a href="{{ $_product->generate_url }}"
                               rel="nofollow">
                                @if($_product->preview_fid)
                                    {!! $_product->_preview_asset('shopProductThumb_110', ['only_way' => FALSE, 'attributes' => ['alt' => strip_tags($_product->title), 'class' => 'img-responsive']]) !!}
                                @else
                                    {!! image_render(NULL, 'shopProductThumb_110', ['no_last_modify' => FALSE, 'only_way' => FALSE, 'attributes'=> ['alt' => strip_tags($_product->title), 'class' => 'img-responsive']]) !!}
                                @endif
                            </a>
                        </div>
                        <div class="col-md-6 hidden-xs col-sm-5">
                            <div class="name">
                                @l($_product->title, $_product->generate_url, ['attributes' => ['target' => '_blank', 'class' => 'title-product']])
                            </div>
                            <div class="attributes">
                                @lang('shop.labels.sky'): {{ $_product->sky }}
                            </div>
                            <div class="comment">
                            <textarea name="comment"
                                      data-path="{{ _r('ajax.add_comment_to_product_in_wish_list', ['list' => $_list->id, 'product' => $_product->id]) }}"
                                      placeholder="@lang('forms.fields.wish_list.add_comment_to_product')"
                                      rows="1">{{ $_product->pivot->comment }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-1 hidden-xs col-sm-3 price">
                            <div>
                                @if($_product->price['view_price'])
                                    {!! '<span>'. $_product->price['price']['format']['view_price'] . '</span> ' . $_product->price['price']['currency']['suffix'] !!}
                                @else
                                    <i>@lang('shop.product.calculation')</i>
                                @endif
                            </div>
                            <button type="button"
                                    rel="add-to-compare"
                                    data-product="{{ $_product->id }}">
                                @lang('forms.buttons.wish_list.add_to_compare')
                            </button>
                        </div>
                        <div class="col-md-3 hidden-xs col-sm-4 action">
                            @if($_product->price['view_price'])
                                <button type="button"
                                        data-path="{{ _r('ajax.shop_action_basket', ['shop_product' => $_product]) }}"
                                        rel="add-to-cart"
                                        class="use-ajax">
                                    @lang('forms.buttons.buy.submit')
                                </button>
                            @else
                                <button type="button"
                                        data-path="{{ _r('ajax.shop_submit_application') }}"
                                        data-product="{{ $_product->id }}"
                                        rel="add-to-cart"
                                        class="use-ajax">
                                    @lang('forms.buttons.buy.order')
                                </button>
                            @endif
                            <button type="button"
                                    class="use-ajax"
                                    data-path="{{ _r('ajax.remove_product_in_wish_list', ['list' => $_list->id, 'product' => $_product->id]) }}"
                                    rel="delete-fron-list">
                                @lang('forms.buttons.wish_list.remove_from_list')
                            </button>
                        </div>
                        <div class="col-xs-12 visible-xs">
                            <div class="name">
                                @l($_product->title, $_product->generate_url, ['attributes' => ['target' => '_blank', 'class' => 'title-product']])
                            </div>
                            <div class="attributes">
                                @lang('shop.labels.sky'): {{ $_product->sky }}
                            </div>
                            <div class="comment">
                                <textarea name="comment"
                                          data-path="{{ _r('ajax.add_comment_to_product_in_wish_list', ['list' => $_list->id, 'product' => $_product->id]) }}"
                                          placeholder="@lang('forms.fields.wish_list.add_comment_to_product')"
                                          rows="1">{{ $_product->pivot->comment }}</textarea>
                            </div>
                            <div class="price">
                                <div>
                                    @if($_product->price['view_price'])
                                        {!! '<span>'. $_product->price['price']['format']['view_price'] . '</span> ' . $_product->price['price']['currency']['suffix'] !!}
                                    @else
                                        <i>@lang('shop.product.calculation')</i>
                                    @endif
                                </div>
                                <button type="button"
                                        rel="add-to-compare"
                                        data-product="{{ $_product->id }}">
                                    @lang('forms.buttons.wish_list.add_to_compare')
                                </button>
                            </div>
                            <div class="action">
                                @if($_product->price['view_price'])
                                    <button type="button"
                                            data-path="{{ _r('ajax.shop_action_basket', ['shop_product' => $_product]) }}"
                                            rel="add-to-cart"
                                            class="use-ajax">
                                        @lang('forms.buttons.buy.submit')
                                    </button>
                                @else
                                    <button type="button"
                                            data-path="{{ _r('ajax.shop_submit_application') }}"
                                            data-product="{{ $_product->id }}"
                                            rel="add-to-cart"
                                            class="use-ajax">
                                        @lang('forms.buttons.buy.order')
                                    </button>
                                @endif
                                <button type="button"
                                        class="use-ajax"
                                        data-path="{{ _r('ajax.remove_product_in_wish_list', ['list' => $_list->id, 'product' => $_product->id]) }}"
                                        rel="delete-fron-list">
                                    @lang('forms.buttons.wish_list.remove_from_list')
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
        <div class="alert alert-warning">
            @lang('frontend.you_have_no_wish_lists')
        </div>
    @endif
</div>