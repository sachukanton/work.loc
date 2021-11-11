<button class="uk-modal-close-outside"
        type="button"
        uk-close></button>
<div class="uk-text-center modal-thanks-content">
    <div class="uk-h2 uk-text-danger">
        @lang('frontend.attention')
    </div>
    <div class="top-panel">
        @variable('warning_text_when_checking_availability')
    </div>
    <div class="uk-margin-bottom">
        @foreach($_availability as $_pharmacy_id => $_products)
            <div class="uk-heading-line uk-text-color-grey uk-margin-small-bottom uk-text-center checkout-title">
            <span>
                @if($_products['pharmacy'])
                    @l("{$_products['pharmacy']->breadcrumb_title}, {$_products['pharmacy']->address}", $_products['pharmacy']->breadcrumb_title, ['attributes' => ['target' => 'blank']])
                @else
                    <span>
                        @lang('shop.labels.pharmacy_store')
                    </span>
                @endif
            </span>
            </div>
            @foreach($_products['products'] as $_product)
                <div class="uk-margin-small-bottom uk-grid-small uk-flex-middle uk-text-left"
                     uk-grid>
                    <div class="uk-width-expand">
                        @l(str_limit(strip_tags($_product['product']->title), 50), $_product['product']->generate_url, ['attributes' => ['title' => strip_tags($_product['product']->title), 'class' => 'uk-link-color-black uk-text-uppercase uk-text-bold']])
                    </div>
                    <div class="uk-width-100 uk-text-right uk-text-small">
                        @if($_product['in_store'] <= 0)
                            <span class="uk-text-danger">
                            @lang('shop.product.not_available')
                        </span>
                        @else
                            <span class="uk-text-warning">
                            @lang('shop.product.not_enough')
                        </span>
                        @endif
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
    <div class="bottom-panel uk-text-small">
        @variable('warning_text_when_checking_availability_2')
    </div>
</div>
