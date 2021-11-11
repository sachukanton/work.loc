@php
    $_device_type = $_wrap['device']['type'];
@endphp
@if($_item->consistProduct->isNotEmpty())
    <div class="open_item_wrapper">
        <div class="swiper-container sets_item">
            <div class="swiper-wrapper">
                {{--@if($_device_type == 'pc')--}}
                            @foreach($_item->consistProduct as $_product)
                                {{--@if($loop->index < 4)--}}
                                    @include('frontend.default.shops.product_teaser_consist', ['_item' => $_product, '_class' => ''])
                                {{--@endif--}}
                            @endforeach
                    {{--@if(count($_item->consistProduct) >= 4)--}}
                        {{--@php--}}
                            {{--$_count_product = count($_item->consistProduct) - 4;--}}
                        {{--@endphp--}}
                        {{--@if($_count_product != 0)--}}
                            {{--<div class="uk-text-center">--}}
                                {{--<a href="" class="link-count-product uk-button uk-link uk-link-consist">--}}
                                    {{--Ще {{ $_count_product }} позиція--}}
                                {{--</a>--}}
                            {{--</div>--}}
                        {{--@endif--}}
                        {{--<div class="last-line last-line-consist" style="display: none">--}}
                            {{--<ul class="uk-child-width-1-4 uk-grid-medium uk-grid"--}}
                                {{--uk-height-match="target: .title-product">--}}
                                {{--@foreach($_item->consistProduct as $_product)--}}
                                    {{--@if($loop->index >= 4)--}}
                                        {{--@include('frontend.default.shops.product_teaser_consist', ['_item' => $_product, '_class' => ''])--}}
                                    {{--@endif--}}
                                {{--@endforeach--}}
                            {{--</ul>--}}
                        {{--</div>--}}
                    {{--@endif--}}
                {{--@else--}}
                    {{--<div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slider>--}}
                        {{--<ul class="uk-slider-items uk-grid-collapse uk-child-width-1-4" uk-height-match="target: .title-product">--}}
                            {{--@foreach($_item->consistProduct as $_product)--}}
                                {{--@if($loop->index < 2)--}}
                                    {{--@include('frontend.default.shops.product_teaser_consist', ['_item' => $_product, '_class' => ''])--}}
                                {{--@endif--}}
                            {{--@endforeach--}}
                        {{--</ul>--}}
                        {{--<a class="uk-position-center-left uk-position-small" href="#" uk-slider-item="previous">--}}
                            {{--<img src="{{ formalize_path('template/images/arrow-left.svg') }}"--}}
                                 {{--alt="">--}}
                        {{--</a>--}}
                        {{--<a class="uk-position-center-right uk-position-small" href="#" uk-slider-item="next">--}}
                            {{--<img src="{{ formalize_path('template/images/arrow-right.svg') }}"--}}
                                 {{--alt="">--}}
                        {{--</a>--}}
                    {{--</div>--}}
                    {{--@if(count($_item->consistProduct) >= 2)--}}
                        {{--@php--}}
                            {{--$_count_product = count($_item->consistProduct) - 2;--}}
                        {{--@endphp--}}
                        {{--@if($_count_product != 0)--}}
                            {{--<div class="uk-text-center">--}}
                                {{--<a href="" class="link-count-product uk-button uk-link uk-link-consist">--}}
                                    {{--Ще {{ $_count_product }} позиція--}}
                                {{--</a>--}}
                            {{--</div>--}}
                        {{--@endif--}}
                        {{--<div class="last-line last-line-consist" style="display: none">--}}
                            {{--<ul class="uk-child-width-1-2 uk-grid-medium uk-grid"--}}
                                {{--uk-height-match="target: .title-product">--}}
                                {{--@foreach($_item->consistProduct as $_product)--}}
                                    {{--@if($loop->index >= 2)--}}
                                        {{--@include('frontend.default.shops.product_teaser_consist', ['_item' => $_product, '_class' => ''])--}}
                                    {{--@endif--}}
                                {{--@endforeach--}}
                            {{--</ul>--}}
                        {{--</div>--}}
                    {{--@endif--}}
                {{--@endif--}}
            </div>
        </div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>
@endif
