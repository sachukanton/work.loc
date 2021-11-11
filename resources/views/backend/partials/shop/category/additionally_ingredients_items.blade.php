<div class="uk-form-row">
    <div id="list-additionally-ingredients-select-items">
        @if($_items->isNotEmpty())
            @include('backend.partials.shop.category.additionally_ingredients_item', compact('_items'))
        @else
            <div class="uk-alert uk-alert-warning uk-border-rounded"
                 uk-alert>
                Список пуст
            </div>
        @endif
    </div>
    <div class="uk-clearfix uk-text-right">
        {!! _l('Добавить элемент', 'oleus.shop_categories.additional_item', ['p' => ['category' => $entity->id, 'action' => 'add'], 'attributes' => ['class' => 'uk-button uk-button-success use-ajax']]) !!}
    </div>
</div>
