<div class="uk-margin">
    <div id="list-product-consists-select-items">
        @isset($_items)
            @if($_items->isNotEmpty())
                @include('backend.partials.shop.product.consists_item', compact('_items', 'entity'))
            @else
                <div class="uk-alert uk-alert-warning uk-border-rounded"
                     uk-alert>
                    Список элементов пуст
                </div>
            @endif
        @endisset
    </div>
    <div class="uk-clearfix uk-text-right">
        @l('Добавить товар', 'oleus.shop_products.consist', ['p' => [ 'shop_product' => $entity, 'action' => 'add'], 'attributes' => ['class' => 'uk-button uk-button-medium uk-button-success use-ajax']])
    </div>
</div>