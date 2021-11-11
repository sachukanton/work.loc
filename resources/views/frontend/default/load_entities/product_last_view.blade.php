@if($_items->isNotEmpty())
    <div class="uk-position-relative uk-margin-medium-top product-last-view">
        <div class="filter-param-title uk-text-uppercase uk-margin-medium-bottom">
            @lang('shop.titles.view_list_last_viewed')
        </div>
        <div uk-slider="autoplay: true;">
            <div class="uk-position-relative">
                <div class="uk-slider-container uk-light">
                    <ul class="uk-slider-items uk-child-width-1-1 uk-grid uk-grid-collapse">
                        @foreach($_items as $_product)
                            <li>
                                @include('frontend.default.shops.product_teaser', ['_item' => $_product])
                            </li>
                        @endforeach
                    </ul>
                </div>
                <a class="uk-position-center-left-out uk-hidden-hover"
                   href="#"
                   uk-slidenav-previous
                   uk-slider-item="previous"></a>
                <a class="uk-position-center-right-out uk-hidden-hover"
                   href="#"
                   uk-slidenav-next
                   uk-slider-item="next"></a>
            </div>
        </div>
    </div>
@endif