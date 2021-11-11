<div id="form-checkout-order-products"
     class="uk-position-relative uk-margin-medium-bottom">
    <div class="uk-h2 uk-heading-divider">
        @lang('shop.titles.your_order')
    </div>
    <div class="uk-position-relative uk-margin-bottom"
         uk-slider>
        <ul class="uk-slider-items uk-child-width-1-4 uk-grid-small"
            uk-grid
            uk-height-match="row: false">
            @foreach($_items as $_product)
                <li class="uk-padding-small-top uk-padding-small-bottom"
                    id="basket-product-{{ $_product->id }}">
                    @include('backend.base.shop_product_checkout', ['_item' => $_product, '_index' => $loop->index])
                </li>
            @endforeach
        </ul>
    </div>
</div>