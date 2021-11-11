<div class="uk-margin">
    <div id="list-menu-items">
        @isset($items)
            @if($items->isNotEmpty())
                @include('backend.partials.menu.items_table', compact('items'))
            @else
                <div class="uk-alert uk-alert-warning uk-border-rounded"
                     uk-alert>
                    Список элементов пуст
                </div>
            @endif
        @endisset
    </div>
    <div class="uk-clearfix uk-text-right">
        <div class="uk-button-group">
            @if($items && $items->count() > 1)
                <a href="{{ _r('oleus.menus.sort', ['menu' => $entity->id]) }}"
                   class="uk-button uk-button-medium uk-button-primary uk-button-save-sorting">
                    Сохранить сортировку
                </a>
            @endif
            @l('Добавить пункт меню', 'oleus.menus.item', ['p' => ['menu' => $entity->id, 'action' => 'add'], 'attributes' => ['class' => 'uk-button uk-button-medium uk-button-success use-ajax']])
        </div>
    </div>
</div>