<div class="panel panel-default order-box">
    <div class="panel-heading header-sidebar"
         role="tab"
         id="heading-{{ $_order->id }}">
        <a role="button"
           data-toggle="collapse"
           data-parent="#uk-items-list"
           href="#collapse-{{ $_order->id }}"
           aria-expanded="true"
           aria-controls="collapse-{{ $_order->id }}">
            <div class="flex">
                <div class="header">
                    @lang('shop.labels.order', ['order' => $_order->id])
                </div>
                <div class="date hidden-sm hidden-xs">
                    {{ $_order->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="info hidden-sm hidden-xs">
                    {!! trans('forms.labels.checkout.total_amount_2', ['product' => plural_string($_order->quantity_in, 'shop.product.not_plural|shop.product.plural|shop.product.plurals|shop.product.plurals2'), 'amount' => '<span>' . $_order->amount['format']['view_price'] . '</span> ' . $_order->amount['currency']['suffix']]) !!}
                </div>
                <div class="action status-{{ $_order->status }}">
                    @lang($_order::ORDER_STATUS[$_order->status])
                </div>
            </div>
            <div class="visible-xs visible-sm">
                <div class="flex">
                    <div class="date">
                        {{ $_order->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div class="info">
                        {!! trans('forms.labels.checkout.total_amount_2', ['product' => plural_string($_order->quantity_in, 'shop.product.not_plural|shop.product.plural|shop.product.plurals|shop.product.plurals2'), 'amount' => '<span>' . $_order->amount['format']['view_price'] . '</span> ' . $_order->amount['currency']['suffix']]) !!}
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div id="collapse-{{ $_order->id }}"
         class="panel-collapse collapse"
         role="tabpanel"
         aria-labelledby="heading-{{ $_order->id }}">
        <div class="row header-sidebar hidden-xs">
            <div class="col-sm-8 col-sm-offset-4">
                <div class="flex flex-table">
                    <div class="sky hidden-sm hidden-xs">
                        @lang('shop.labels.sky')
                    </div>
                    <div class="price">
                        @lang('shop.labels.price')
                    </div>
                    <div class="counter">
                        @lang('shop.labels.quantity')
                    </div>
                    <div class="amount">
                        @lang('shop.labels.amount')
                    </div>
                    <div class="action"></div>
                </div>
            </div>
        </div>
        @foreach($_order->_products as $_product)
            @php
                $_entity = $_product->_product ?: NULL;
                if($_entity) $_entity->price = $_entity->_render_price();
            @endphp
            <div class="row product-sidebar"
                 id="order-{{ $_order->id }}-product-{{ $_product->id }}">
                <div class="col-sm-4">
                    @if($_entity)
                        @l($_entity->title, $_entity->generate_url, ['attributes' => ['target' => '_blank', 'class' => 'title-product']])
                    @else
                        <div class="title-product no-link">
                            {{ $_product->product_name }}
                        </div>
                    @endif
                </div>
                <div class="col-sm-8">
                    <div class="flex flex-table">
                        <div class="sky hidden-sm hidden-xs">
                            @if($_entity)
                                {{ $_entity->sky }}
                            @else
                                -//-
                            @endif
                        </div>
                        <div class="price">
                            <div class="visible-xs label-price">
                                @lang('shop.labels.price')
                            </div>
                            {!! '<span>'. $_product->price['format']['view_price'] . '</span> ' . $_product->price['currency']['suffix'] !!}
                        </div>
                        <div class="counter">
                            <div class="visible-xs label-price">
                                @lang('shop.labels.quantity')
                            </div>
                            {{ $_product->quantity }}
                        </div>
                        <div class="amount">
                            <div class="visible-xs label-price">
                                @lang('shop.labels.amount')
                            </div>
                            {!! '<span>'. $_product->amount['format']['view_price'] . '</span> ' . $_product->amount['currency']['suffix'] !!}
                        </div>
                        <div class="action">
                            @if($_entity)
                                <button type="button"
                                        rel="add-product-to-basket"
                                        data-product="{{ $_entity->id }}"
                                        {{ $_entity->price['view_price'] ? NULL : 'disabled' }}
                                        class="add">
                                    <span class="hidden-sm hidden-xs">
                                        @lang('shop.labels.add_product')
                                    </span>
                                    <span class="visible-sm visible-xs">
                                        <img src="{{ formalize_path('template/svg/basket2.svg') }}"
                                             height="20"
                                             alt="">
                                    </span>
                                </button>
                            @else
                                <button type="button"
                                        disabled
                                        rel="add-product-to-basket"
                                        class="add">
                                    <span class="hidden-sm hidden-xs">
                                        @lang('shop.labels.add_product')
                                    </span>
                                    <span class="visible-sm visible-xs">
                                        <img src="{{ formalize_path('template/svg/basket3.svg') }}"
                                             height="20"
                                             alt="">
                                    </span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @if($_order->_attach_file)
            <div style="padding: 5px 10px 8px; border-top: 1px #ddd solid; margin-top: 10px; text-align: right;">
                @l(trans('shop.labels.download_invoice'), $_order->_attach_file->base_url, ['attributes' => ['target' => '_blank', 'title' => $_order->_attach_file->filename]])
            </div>
        @endif
    </div>
</div>