@if($_item->relatedProduct->isNotEmpty())
    <div class="uk-position-relative uk-margin-medium-top uk-margin-medium-bottom">
        <div class="uk-h2 uk-text-bold uk-heading-divider uk-margin-remove-top">
            @lang('shop.titles.view_list_recommended')
        </div>
        <div class="uk-position-relative uk-visible-toggle"
             tabindex="-1"
             uk-slider>
            <ul class="uk-slider-items uk-child-width-1-4 uk-grid-small"
                uk-grid
                uk-height-match="row: false">
                @foreach($_item->relatedProduct as $_product)
                    <li class="uk-padding-small-top uk-padding-small-bottom">
                        @include('backend.base.shop_product_teaser', ['_item' => $_product, '_class' => 'uk-height-1-1 uk-padding-small'])
                    </li>
                @endforeach
            </ul>
            <a class="uk-position-center-left uk-position-small uk-hidden-hover"
               href="#"
               uk-slidenav-previous
               uk-slider-item="previous"></a>
            <a class="uk-position-center-right uk-position-small uk-hidden-hover"
               href="#"
               uk-slidenav-next
               uk-slider-item="next"></a>
        </div>
    </div>
@endif