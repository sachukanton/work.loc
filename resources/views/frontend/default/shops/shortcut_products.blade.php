@if($_items->isNotEmpty())
    <div class="more__items for_blog">
    <h4>{{variable('recomender')}}</h4>
        <div class=" wrapper">
            @foreach($_items as $_product)
                    @include('frontend.default.shops.product_teaser', ['_item' => $_product])
            @endforeach
        </div>
    </div>
@endif
