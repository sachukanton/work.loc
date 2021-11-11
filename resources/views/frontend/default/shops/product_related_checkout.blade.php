
@if($_item->recommended_checkout->isNotEmpty())
    <div class="uk-position-relative product-related">
        <div class="uk-container uk-container-xlarge">
            <div class="uk-grid">
                <div class="uk-width-auto">
                    <h2 class="title-02 uk-position-relative uk-position-z-index">
                        @lang('frontend.titles.view_list_recommended'):
                    </h2>
                </div>
                <div class="uk-flex uk-flex-wrap uk-flex-right uk-flex-middle uk-width-expand@m icons-full">
                    <div class="uk-label-box icon-img-full uk-position-relative">
                        <label>
                            <input class="uk-checkbox" type="checkbox">
                            <span>
                            крупное фото
                        </span>
                        </label>
                    </div>
                    <div class="uk-label-box icon-consist-full uk-position-relative">
                        <label>
                            <input class="uk-checkbox" type="checkbox">
                            <span>
                            отображать состав
                        </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="product-related-content @if($_device_type != 'pc') related-mb @endif">
                @if($_device_type == 'pc')
                    <div class="first-line">
                        <ul class="uk-child-width-1-4 uk-grid-large uk-grid" uk-height-match="target: .title-product">
                            @foreach($_item->recommended_checkout as $_product)
                                @if($loop->index < 4)
                                    @include('frontend.default.shops.product_teaser', ['_item' => $_product, '_class' => ''])
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @if(count($_item->recommended_checkout) >= 4)
                        @php
                            $_count_product = count($_item->recommended_checkout) - 4;
                        @endphp
                        @if($_count_product != 0)
                            <div class="uk-text-center">
                                <a href="" class="link-count-product uk-button uk-link">
                                    Ще {{ $_count_product }} позиція
                                </a>
                            </div>
                        @endif
                        <div class="last-line" style="display: none">
                            <ul class="uk-child-width-1-4 uk-grid-large uk-grid"
                                uk-height-match="target: .title-product">
                                @foreach($_item->recommended_checkout as $_product)
                                    @if($loop->index >= 4)
                                        @include('frontend.default.shops.product_teaser', ['_item' => $_product, '_class' => ''])
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @else
                    <div class="first-line">
                        <ul class="uk-child-width-1-2 uk-grid-large uk-grid" uk-height-match="target: .title-product">
                            @foreach($_item->recommended_checkout as $_product)
                                @if($loop->index < 2)
                                    @include('frontend.default.shops.product_teaser', ['_item' => $_product, '_class' => ''])
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @if(count($_item->recommended_checkout) >= 2)
                        @php
                            $_count_product = count($_item->recommended_checkout) - 2;
                        @endphp
                        @if($_count_product != 0)
                            <div class="uk-text-center">
                                <a href="" class="link-count-product uk-button uk-link">
                                    Ще {{ $_count_product }} позиція
                                </a>
                            </div>
                        @endif
                        <div class="last-line" style="display: none">
                            <ul class="uk-child-width-1-2 uk-grid-large uk-grid"
                                uk-height-match="target: .title-product">
                                @foreach($_item->recommended_checkout as $_product)
                                    @if($loop->index >= 2)
                                        @include('frontend.default.shops.product_teaser', ['_item' => $_product, '_class' => ''])
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endif
                {{--<ul class="uk-child-width-1-4@l uk-child-width-1-3@m uk-child-width-1-2 uk-grid-large uk-grid">--}}
                {{--@foreach($_item->relatedProduct as $_product)--}}
                {{--@include('frontend.default.shops.product_teaser', ['_item' => $_product, '_class' => 'uk-height-1-1'])--}}
                {{--@endforeach--}}
                {{--</ul>--}}
            </div>
        </div>
    </div>
@endif