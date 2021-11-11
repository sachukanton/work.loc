@if($_items->isNotEmpty())
    <section class="new__section">
        <div class="container">
                @isset($_title)
                    <h2>
                        {!! $_title !!}
                    </h2>
                @endisset
            <div class="swiper-container category_open">
                <div class="swiper-btn">
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
                <div class="swiper-wrapper">
                @foreach($_items as $_product)
                    @include('frontend.default.shops.product_teaser', ['_item' => $_product])
                @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
