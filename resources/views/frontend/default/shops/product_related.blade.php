@if($_item->relatedProduct->isNotEmpty())
    <section class="more__items">
        <div class="container">
                <h4>{{variable('recomender')}}</h4>
            <div class="wrapper">
                {{--@if($_device_type == 'pc')--}}
                    {{--<div class="first-line">--}}
                                @foreach($_item->relatedProduct as $_product)
                                    {{--@if($loop->index < 4)--}}
                                        @include('frontend.default.shops.product_teaser', ['_item' => $_product, '_class' => ''])
                                    {{--@endif--}}
                                @endforeach
                                {{--<a class="uk-position-center-left" href="#" uk-slider-item="previous">--}}
                                    {{--<img src="{{ formalize_path('template/images/arrow-left.svg') }}"--}}
                                         {{--alt="">--}}
                                {{--</a>--}}
                                {{--<a class="uk-position-center-right" href="#" uk-slider-item="next">--}}
                                    {{--<img src="{{ formalize_path('template/images/arrow-right.svg') }}"--}}
                                         {{--alt="">--}}
                                {{--</a>--}}
                    {{--</div>--}}
                    {{--@if(count($_item->relatedProduct) >= 4)--}}
                        {{--@php--}}
                            {{--$_count_product = count($_item->relatedProduct) - 4;--}}
                        {{--@endphp--}}
                        {{--@if($_count_product != 0)--}}
                            {{--<div class="uk-text-center">--}}
                                {{--<a href="" class="link-count-product uk-button uk-link uk-link-related">--}}
                                    {{--Ще {{ $_count_product }} позиція--}}
                                {{--</a>--}}
                            {{--</div>--}}
                        {{--@endif--}}
                        {{--<div class="last-line last-line-related" style="display: none">--}}
                            {{--<ul class="uk-child-width-1-4 uk-grid-large uk-grid"--}}
                                {{--uk-height-match="target: .title-product">--}}
                                {{--@foreach($_item->relatedProduct as $_product)--}}
                                    {{--@if($loop->index >= 4)--}}
                                        {{--@include('frontend.default.shops.product_teaser', ['_item' => $_product, '_class' => ''])--}}
                                    {{--@endif--}}
                                {{--@endforeach--}}
                            {{--</ul>--}}
                        {{--</div>--}}
                    {{--@endif--}}
                {{--@else--}}
                    {{--<div class="first-line">--}}
                        {{--<ul class="uk-child-width-1-2 uk-grid-large uk-grid" uk-height-match="target: .title-product">--}}
                            {{--@foreach($_item->relatedProduct as $_product)--}}
                                {{--@if($loop->index < 2)--}}
                                    {{--@include('frontend.default.shops.product_teaser', ['_item' => $_product, '_class' => ''])--}}
                                {{--@endif--}}
                            {{--@endforeach--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                    {{--@if(count($_item->relatedProduct) >= 2)--}}
                        {{--@php--}}
                            {{--$_count_product = count($_item->relatedProduct) - 2;--}}
                        {{--@endphp--}}
                        {{--@if($_count_product != 0)--}}
                            {{--<div class="uk-text-center">--}}
                                {{--<a href="" class="link-count-product uk-button uk-link uk-link-related">--}}
                                    {{--Ще {{ $_count_product }} позиція--}}
                                {{--</a>--}}
                            {{--</div>--}}
                        {{--@endif--}}
                        {{--<div class="last-line last-line-related" style="display: none">--}}
                            {{--<ul class="uk-child-width-1-2 uk-grid-large uk-grid"--}}
                                {{--uk-height-match="target: .title-product">--}}
                                {{--@foreach($_item->relatedProduct as $_product)--}}
                                    {{--@if($loop->index >= 2)--}}
                                        {{--@include('frontend.default.shops.product_teaser', ['_item' => $_product, '_class' => ''])--}}
                                    {{--@endif--}}
                                {{--@endforeach--}}
                            {{--</ul>--}}
                        {{--</div>--}}
                    {{--@endif--}}
                {{--@endif--}}
                {{--<ul class="uk-child-width-1-4@l uk-child-width-1-3@m uk-child-width-1-2 uk-grid-large uk-grid">--}}
                {{--@foreach($_item->relatedProduct as $_product)--}}
                {{--@include('frontend.default.shops.product_teaser', ['_item' => $_product, '_class' => 'uk-height-1-1'])--}}
                {{--@endforeach--}}
                {{--</ul>--}}
            </div>
        </div>
    </section>
@endif