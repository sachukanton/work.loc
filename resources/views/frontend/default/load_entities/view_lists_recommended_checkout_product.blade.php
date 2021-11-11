@if($_items->isNotEmpty())
    <section class="new__section">
        <div class="container">
            @isset($_title)
                <h2>
                    {!! $_title !!}
                </h2>
            @endisset
            <div class="category_open_checkout">
                @foreach($_items as $_product)
                    @include('frontend.default.shops.product_teaser_checkout', ['_item' => $_product])
                @endforeach
            </div>
        </div>
    </section>
@endif
