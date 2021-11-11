<div class="uk-margin">
    <div id="list-param-select-items">
        @isset($items)
            @if($items->isNotEmpty())
                @include('backend.partials.shop.param_item.item', compact('items'))
            @else
                <div class="uk-alert uk-alert-warning uk-border-rounded"
                     uk-alert>
                    Список элементов пуст
                </div>
            @endif
        @endisset
    </div>
    <div class="uk-clearfix uk-text-right">
        @l('Добавить элемент списка', 'oleus.shop_params.item', ['p' => ['param' => $entity->id, 'action' => 'add'], 'attributes' => ['class' => 'uk-button uk-button-medium uk-button-success use-ajax']])
    </div>
</div>