<div class="uk-form-row">
    <div id="list-advantage-items">
        @isset($items)
            @if($items->isNotEmpty())
                @include('backend.partials.advantage.items_table', compact('items'))
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
                <a href="{{ _r('oleus.advantages.sort', ['advantage' => $entity->id]) }}"
                   class="uk-button uk-button-medium uk-button-primary uk-button-save-sorting">
                    Сохранить сортировку
                </a>
            @endif
            @l('Добавить', 'oleus.advantages.item', ['p' => ['entity' => $entity->id, 'action' => 'add'], 'attributes' => ['class' => 'uk-button uk-button-medium uk-button-success use-ajax']])
        </div>
    </div>
</div>