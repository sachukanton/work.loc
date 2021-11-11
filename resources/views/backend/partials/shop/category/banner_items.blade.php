<div class="uk-margin">
    <div id="list-category-banners-items">
        @isset($_items)
            @if($_items->isNotEmpty())
                @include('backend.partials.shop.category.banner_item', compact('_items'))
            @else
                <div class="uk-alert uk-alert-warning uk-border-rounded"
                     uk-alert>
                    Список элементов пуст
                </div>
            @endif
        @endisset
    </div>
    <div class="uk-clearfix uk-text-right">
        @l('Выбрать баннер', 'oleus.shop_categories.banner', ['p' => ['shop_category' => $entity->id, 'action' => 'add'], 'attributes' => ['class' => 'uk-button uk-button-medium uk-button-success use-ajax' . ($_items->isNotEmpty() ? ' uk-hidden' : NULL)]])
    </div>
</div>
